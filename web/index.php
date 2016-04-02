<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Memtext\Form\LoginForm;
use \Memtext\Form\TextForm;
use \Memtext\Auth\LoginManager;
use \Memtext\Mapper\UserMapper;
use \Memtext\Mapper\TextMapper;
use \Memtext\Service\TranslatorService;

require '../vendor/autoload.php';

$settings['displayErrorDetails'] = true;
$settings['db']['dbname'] = 'memtext';
$settings['db']['user'] = 'root';
$settings['db']['pass'] = '';
$settings['db']['host'] = 'localhost';

$app = new \Slim\App(['settings' => $settings]);

$container = $app->getContainer();

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

$container['userFactory'] = function ($c) {
    $fieldList = ['id', 'login', 'email', 'saltedHash', 'salt'];
    return new \Memtext\Factory\UserFactory($fieldList);
};

$container['userMapper'] = function ($c) {
    return new UserMapper($c['connection']);
};

$container['textMapper'] = function ($c) {
    return new TextMapper($c['connection']);
};

$container['loginManager'] = function ($c) {
    return new LoginManager($c['userMapper']);
};

$container['translatorService'] = function ($c) {
    return new TranslatorService($c['textMapper']);
};

$container['view'] = new \Slim\Views\Twig('../templates');
$container['view']->addExtension(new \Twig_Extensions_Extension_Text());
$container['yandexApiKey'] = 'trnsl.1.1.20160330T163001Z.d161a299772702fe.' .
                                '0d436c4c1cfc1713dea2aeb9d9e3f2bebae02844';

$app->map(
    ['GET', 'POST'],
    '/',
    function (Request $request, Response $response) {
        $userTexts = [];
        $textForm = new TextForm($request);
        if ($this->loginManager->isLogged()) {
            if ($request->isPost()) {
                if ($textForm->validate()) {
                    /*$textId = $this->textMapper->save(
                        $textForm->content,
                        $this->loginManager->getUserId()
                    );*/
                    $words = $this->translatorService->parseText($textForm->content);
                    var_dump($words);
                    //...
                }
            }
            $userId = $this->loginManager->getUserId();
            $userTexts = $this->textMapper->findAllByUserId($userId);
        }
        $response = $this->view->render(
            $response,
            'main_page.twig',
            [
                'loginManager' => $this->loginManager,
                'userTexts' => $userTexts,
                'textForm' => $textForm
            ]
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

$app->post('/logout', function (Request $request, Response $response) {
    $this->loginManager->logout();
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->run();
