<?php

$settings['displayErrorDetails'] = true;

$settings['db'] = [
    'dbname' => 'memtext',
    'user' => 'root',
    'pass' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$settings['sphinx'] = [
    'host' => '127.0.0.1',
    'port' => 9306,
    'indexName' => 'ix_dictionary',
];

$settings['pager'] = [
    'perPage' => 20,
    'maxLinksCount' => 6,
];

$settings['purifier'] = [
    'AutoFormat.AutoParagraph' => true,
    'AutoFormat.RemoveEmpty' => true,
    'HTML.Doctype' => 'HTML 4.01 Transitional',
    'HTML.AllowedElements' =>
        ['p','h1','h2','h3','h4','h5','h6','br','em','b','i','strong'],
];

$settings['yandex'] = [
    'key' => 'trnsl.1.1.20160330T163001Z.d161a299772702fe.'.
             '0d436c4c1cfc1713dea2aeb9d9e3f2bebae02844',
    'api' => 'https://translate.yandex.net/api/v1.5/tr.json/translate',
];
