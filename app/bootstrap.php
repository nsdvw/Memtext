<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require "../vendor/autoload.php";
require "../config/settings.php";

$devMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    [__DIR__ . "/Model"],
    $devMode
);
$config->addEntityNamespace('Memtext', 'Memtext\Model');

$db = $settings['db'];
$conn = [
    'host' => $db['host'],
    'dbname' => $db['dbname'],
    'user' => $db['user'],
    'pass' => $db['pass'],
    'driver' => $db['driver'],
];

$em = EntityManager::create($conn, $config);
