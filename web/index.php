<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Memtext\Form\LoginForm;
use \Memtext\Form\RegisterForm;
use \Memtext\Form\TextForm;
use \Memtext\Auth\LoginManager;
use \Memtext\Mapper\UserMapper;
use \Memtext\Mapper\TextMapper;
use \Memtext\Mapper\WordMapper;
use \Memtext\Service\TranslatorService;
use \Memtext\Handler\NotFoundHandler;
use \Memtext\Helper\Pager;
use \Memtext\Helper\TextParser;
use \Memtext\Helper\YandexTranslator;
use \Memtext\Redis\RediskaAdapter;
use \Memtext\Model\Text;

session_start();

require '../vendor/autoload.php';

$settings['displayErrorDetails'] = true;
$settings['db']['dbname'] = 'memtext';
$settings['db']['user'] = 'root';
$settings['db']['pass'] = '';
$settings['db']['host'] = 'localhost';
$settings['pager']['perPage'] = 20;
$settings['pager']['maxLinksCount'] = 6;
$settings['yandex']['key'] = 'trnsl.1.1.20160330T163001Z.d161a299772702fe.' .
                        '0d436c4c1cfc1713dea2aeb9d9e3f2bebae02844';
$settings['yandex']['api'] = 'https://translate.yandex.net/api/v1.5/tr.json/translate';

$app = new \Slim\App(['settings' => $settings]);

$container = $app->getContainer();

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

$container['connection'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['dbname']}",
        $db['user'],
        $db['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['userMapper'] = function ($c) {
    return new UserMapper($c['connection']);
};

$container['textMapper'] = function ($c) {
    return new TextMapper($c['connection']);
};

$container['wordMapper'] = function ($c) {
    return new WordMapper($c['connection']);
};

$container['loginManager'] = function ($c) {
    return new LoginManager($c['userMapper']);
};

$container['textParser'] = new TextParser;

$container['yandexTranslator'] = function ($c) {
    $settings = $c['settings']['yandex'];
    return new YandexTranslator($settings['api'], $settings['key']);
};

$container['translatorService'] = function ($c) {
    return new TranslatorService(
        $c['textMapper'],
        $c['wordMapper'],
        $c['textParser'],
        $c['yandexTranslator'],
        $c['redisClient']
    );
};

$container['redisClient'] = function ($c) {
    $rediska = new \Rediska();
    return new RediskaAdapter($rediska);
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
        $itemCount = $this->textMapper->getTextCountByUserId($userId);
        $pagerSettings = $this->settings['pager'];

        $pager = new Pager(
            $currentPage,
            $itemCount,
            $pagerSettings['perPage'],
            $pagerSettings['maxLinksCount']
        );

        $userTexts = $this->textMapper->findAllByUserId(
            $userId,
            $pager->perPage,
            $pager->getOffset()
        );

        $this->view['pager'] = $pager;
        $this->view['userTexts'] = $userTexts;
    }

    return $this->view->render($response, 'main_page.twig');
})->add($container->get('csrf'));

$app->get('/text/view/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $text = $this->textMapper->findById($textId);
    return $this->view->render($response, 'view_text.twig', ['text' => $text]);
});

$app->post('/text/delete/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $authorId = $this->textMapper->getAuthorId($textId);
    if ($this->loginManager->isOwner($authorId)) {
        $this->textMapper->delete($textId);
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    return $this->view->render($response, 'forbidden.twig');
});

$app->get('/dict/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $redis = $this->redisClient;
    $key = $redis->getPrefix() . ":{$textId}";
    $dictionary = $redis->hgetall($key);
    return $this->view->render(
        $response,
        'view_dict.twig',
        ['dictionary' => $dictionary, 'textId' => $textId]
    );
});

$app->post('/dict/update/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $authorId = $this->textMapper->getAuthorId($textId);
    if ($this->loginManager->isOwner($authorId)) {
        $postData = $request->getParsedBody()['dictUpdate'];
        $fields = json_decode($postData['fields']);
        $redis = $this->redisClient;
        $key = $redis->getPrefix() . ":{$textId}";
        $redis->hdel($key, $fields);
        return $response->withStatus(302)->withHeader(
            'Location',
            "/dict/{$textId}"
        );
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
        $textForm = new TextForm($request);
        if ($request->isPost()) {
            if ($textForm->validate()) {
                $text = new Text;
                $text->content = $textForm->content;
                $text->title = $textForm->title;
                $text->user_id = $this->loginManager->getUserId();
                $this->textMapper->save($text);
                $words = $this->translatorService->createVocabulary($textForm->content);
                $this->translatorService->saveToRedis($text->id, $words);
                return $response->withStatus(302)->withHeader(
                    'Location',
                    "/text/view/{$text->id}"
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
            if ($this->loginManager->validateRegisterForm($form)) {
                $user = $form->getUser();
                $this->userMapper->register($user);
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
