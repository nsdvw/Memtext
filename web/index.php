<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Memtext\Mapper\SphinxMapper;
use Memtext\Service\TextService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Memtext\Form\LoginForm;
use Memtext\Form\RegisterForm;
use Memtext\Form\TextForm;
use Memtext\Service\LoginManager;
use Memtext\Handler\NotFoundHandler;
use Memtext\Helper\Pager;
use Memtext\Helper\TextParser;
use Memtext\Model\Text;
use Slim\App;

session_start();

require '../vendor/autoload.php';
require '../config/settings.php';

$app = new App(['settings' => $settings]);

$container = $app->getContainer();

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

$container['entityManager'] = function ($c) {
    $devMode = true;
    $config = Setup::createYAMLMetadataConfiguration(
        [dirname(__DIR__) . "/config/yaml"],
        $devMode
    );
    $db = $c['settings']['db'];
    $conn = [
        'host' => $db['host'],
        'dbname' => $db['dbname'],
        'user' => $db['user'],
        'pass' => $db['pass'],
        'driver' => $db['driver'],
    ];
    return EntityManager::create($conn, $config);
};

$container['sphinx_conn'] = function ($c) {
    $dbalConfig = new Configuration();
    $sphinx_config = $c['settings']['sphinx'];
    $driver = $c['settings']['db']['driver'];
    $connectionParams = [
        'host' => $sphinx_config['host'],
        'port' => $sphinx_config['port'],
        'driver' => $driver,
    ];
    return DriverManager::getConnection(
        $connectionParams,
        $dbalConfig
    );
};

$container['sphinxMapper'] = function ($c) {
    return new SphinxMapper($c['sphinx_conn']);
};

$container['loginManager'] = function ($c) {
    return new LoginManager($c['entityManager']->getRepository(
        '\Memtext\Model\User'
    ));
};

$container['purifier'] = function ($c) {
    $config = \HTMLPurifier_Config::createDefault();
    $settings = $c['settings']['purifier'];
    foreach ($settings as $key => $value) {
        $config->set($key, $value);
    }
    return new \HTMLPurifier($settings);
};

$container['textParser'] = new TextParser;

$container['translatorService'] = function ($c) {
    return new TranslatorService(
        $c['textMapper'],
        $c['wordMapper'],
        $c['textParser'],
        $c['yandexTranslator']
    );
};

$container['textService'] = function ($c) {
    return new TextService(
        $c['textParser'],
        $c['entityManager'],
        $c['sphinxService']
    );
};

$container['sphinxService'] = function ($c) {
    $sphinxConfig = $c['settings']['sphinx'];
    return new \Memtext\Service\SphinxService(
        $c['sphinxMapper'],
        $sphinxConfig['short_index'],
        $sphinxConfig['full_index']
    );
};

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('../templates');
    $view->addExtension(new \Twig_Extensions_Extension_Text());
    $view['loginManager'] = $c['loginManager'];
    return $view;
};

$container['notFoundHandler'] = function ($c) {
    return new NotFoundHandler(
        $c['view'],
        function (Request $request, Response $response) use ($c) {
            return $c['response']->withStatus(404);
    });
};

$app->get('/', function (Request $request, Response $response) {

    $nameKey = $this->csrf->getTokenNameKey();
    $valueKey = $this->csrf->getTokenValueKey();

    $this->view['csrf'] = [
        'nameKey' => $nameKey,
        'valueKey' => $valueKey,
        'name' => $request->getAttribute($nameKey),
        'value' => $request->getAttribute($valueKey),
    ];

    if ($this->loginManager->isLogged()) {
        $userId = $this->loginManager->getUserId();
        $currentPage = $request->getQueryParam('page')
                       ? $request->getQueryParam('page') : 1;
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();
        $query = $qb->select('COUNT(t)')
            ->from('\Memtext\Model\Text', 't')
            ->where('t.author=:id')
            ->setParameter('id', $userId)
            ->getQuery();
        $textsCount = $query->getSingleScalarResult();
        $pagerSettings = $this->settings['pager'];

        $pager = new Pager(
            $currentPage,
            $textsCount,
            $pagerSettings['perPage'],
            $pagerSettings['maxLinksCount']
        );

        $userTexts = $this->entityManager->getRepository('\Memtext\Model\Text')
            ->findBy(
                ['author'=>$userId],
                null,
                $pager->perPage,
                $pager->getOffset()
            );

        $this->view['pager'] = $pager;
        $this->view['userTexts'] = $userTexts;
    }

    return $this->view->render($response, 'main_page.twig');
})->add($container->get('csrf'));

$app->get('/test/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $text = $this->entityManager->find('\Memtext\Model\Text', $textId);
    return $this->view->render(
        $response,
        'test.twig',
        ['words' => $text->getShortDictsArray(), 'textId' => $textId]
    );
});

$app->get('/text/view/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $textRepo = $this->entityManager->getRepository('\Memtext\Model\Text');
    $dql = "SELECT t, f, s FROM Memtext\Model\Text t JOIN t.fulldicts f"
           . " JOIN t.shortdicts s WHERE t.id=?1";
    $query = $this->entityManager->createQuery($dql);
    $query->setParameter(1, $textId);
    $textWithWords = $query->getResult()[0];
    return $this->view->render(
        $response, 'view_text.twig', ['text' => $textWithWords]
    );
});

$app->post('/text/delete/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $text = $this->entityManager->find('\Memtext\Model\Text', $textId);
    if ($this->loginManager->isOwner($text->getAuthor()->getId())) {
        $this->entityManager->remove($text);
        $this->entityManager->flush();
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    return $this->view->render($response, 'forbidden.twig');
});

$app->map(
    ['GET', 'POST'],
    '/text/new',
    function (Request $request, Response $response) {
        if (!$this->loginManager->isLogged()) {
            return $response->withStatus(302)->withHeader('Location', '/login');
        }
        $textForm = new TextForm($request, $this->purifier);
        if ($request->isPost()) {
            if ($textForm->validate()) {
                $text = new Text;
                $text->setContent($textForm->content);
                $text->setTitle($textForm->title);
                $text->setAuthor($this->loginManager->getLoggedUser());
                $this->textService->saveWithDictionary($text);
                return $response->withStatus(302)->withHeader(
                    'Location',
                    "/text/view/{$text->getId()}"
                );
            }
        }
        $response = $this->view->render(
            $response,
            'new_text.twig',
            ['textForm' => $textForm]
        );
        return $response;
    }
);

$app->map(
    ['GET', 'POST'],
    '/login',
    function (Request $request, Response $response) {
        $loginForm = new LoginForm($request);
        if ($request->isPost()) {
            if ($this->loginManager->validateLoginForm($loginForm)) {
                $this->loginManager->login(
                    $loginForm->getUser(),
                    $loginForm->remember
                );
                return $response->withStatus(302)->withHeader('Location', '/');
            }
        }
        $response = $this->view->render(
            $response,
            'login_page.twig',
            ['loginForm' => $loginForm]
        );
        return $response;
    }
);

$app->map(
    ['GET', 'POST'],
    '/register',
    function (Request $request, Response $response) {
        $form = new RegisterForm($request);
        if ($request->isPost()) {
            $em = $this->entityManager;
            if ($this->loginManager->validateRegisterForm($form)) {
                $user = $form->getUser();
                $em->persist($user);
                $em->flush();
                $this->loginManager->login($user, $form->remember);
                return $response->withStatus(302)->withHeader('Location', '/');
            }
        }
        return $this->view->render(
            $response,
            'register_page.twig',
            ['registerForm' => $form]
        );
    }
);

$app->post('/logout', function (Request $request, Response $response) {
    $this->loginManager->logout();
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->run();
