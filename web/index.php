<?php

use Memtext\Form\LogoutForm;
use Memtext\Helper\Csrf;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Memtext\Form\LoginForm;
use Memtext\Form\RegisterForm;
use Memtext\Form\TextForm;
use Memtext\Helper\Pager;
use Memtext\Model\Text;
use Slim\App;

require "../app/bootstrap.php";

$token = Csrf::init();

$app = new App(['settings' => $settings]);

require "../app/container.php";

$app->get('/', function (Request $request, Response $response) {

    if ($this->loginManager->isLogged()) {
        $userId = $this->loginManager->getUserId();
        $currentPage = $request->getQueryParam('page')
                       ? $request->getQueryParam('page') : 1;
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();
        $query = $qb->select('COUNT(t)')
            ->from('Memtext:Text', 't')
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

        $userTexts = $this->entityManager->getRepository('Memtext:Text')
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
});

$app->get('/test/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $text = $this->entityManager->find('Memtext:Text', $textId);
    return $this->view->render(
        $response,
        'test.twig',
        ['words' => $text->getShortWordsArray(), 'textId' => $textId]
    );
});

$app->get('/text/view/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $dql = "SELECT t, w FROM Memtext:Text t JOIN t.words w"
           . " WHERE t.id=?1";
    $query = $this->entityManager->createQuery($dql);
    $query->setParameter(1, $textId);
    $textWithWords = $query->getResult()[0];

    return $this->view->render(
        $response, 'view_text.twig', ['text' => $textWithWords]
    );
});

$app->post('/text/delete/{id}', function (Request $request, Response $response) {
    $textId = $request->getAttribute('id');
    $text = $this->entityManager->find('Memtext:Text', $textId);
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
    $form = new LogoutForm($request);
    if ($this->loginManager->checkToken($form->csrf_token)) {
        $this->loginManager->logout();
    }
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->run();
