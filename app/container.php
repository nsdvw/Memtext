<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Memtext\Mapper\SphinxMapper;
use Memtext\Service\TextService;
use Memtext\Service\LoginManager;
use Memtext\Handler\NotFoundHandler;
use Memtext\Helper\TextParser;

$container = $app->getContainer();

$container['csrf_token'] = $token;

// obtain em from bootstrap.php
$container['entityManager'] = $em;

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
    return new LoginManager(
        $c['entityManager']->getRepository('Memtext:User'),
        $c['csrf_token']
    );
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
        $sphinxConfig['indexName']
    );
};

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('../templates');
    $view->addExtension(new \Twig_Extensions_Extension_Text());
    $view['loginManager'] = $c['loginManager'];
    $view['csrf_token'] = $c['csrf_token'];
    return $view;
};

$container['notFoundHandler'] = function ($c) {
    return new NotFoundHandler(
        $c['view'],
        function (Request $request, Response $response) use ($c) {
            return $c['response']->withStatus(404);
        });
};
