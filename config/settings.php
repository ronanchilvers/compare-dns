<?php

use Symfony\Component\Yaml\Yaml;

$settings = [
    // Slim3 settings
    'displayErrorDetails' => false,

    // Logging
    'logger' => [
        'filename' => false
    ],

    // Twig
    'twig' => [
        'templates' => __DIR__ . '/../resources/templates',
        'cache' => __DIR__ . '/../var/twig',
    ],

    // Session settings
    'session' => [
        'encryption.key' => null,
    ],

    // Database connections
    'database' => [
        'name'     => 'app.sq3',
        'dsn'      => 'sqlite:' . __DIR__ . '/../var/database/app.sq3',
        'username' => '',
        'password' => '',
        'options'  => [],
    ],
];

$localYaml = __DIR__ . '/../local.yaml';
if (file_exists($localYaml)) {
    $localSettings = Yaml::parseFile($localYaml);
    $settings = array_replace_recursive($settings, $localSettings);
}

return $settings;
