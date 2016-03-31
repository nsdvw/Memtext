<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$settings['displayErrorDetails'] = true;
$settings['db']['dbname'] = 'memtext';
$settings['db']['user'] = 'root';
$settings['db']['pass'] = '';
$settings['db']['host'] = 'localhost';

$app = new \Slim\App(['settings' => $settings]);

$container = $app->getContainer();

$container['db'] = function ($c) {
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

$container['view'] = new \Slim\Views\Twig('../templates');
$container['yandexApiKey'] = 'trnsl.1.1.20160330T163001Z.d161a299772702fe.' .
                                '0d436c4c1cfc1713dea2aeb9d9e3f2bebae02844';

$app->get('/', function (Request $request, Response $response) {
    $response = $this->view->render($response, 'main_page.twig');
    return $response;
});

$app->run();
