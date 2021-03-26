<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Registry;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Ronanchilvers\Container\ServiceProviderInterface;
use Ronanchilvers\Container\Container;
use Ronanchilvers\Sessions\Session;
use Ronanchilvers\Sessions\Storage\CookieStorage;
use Ronanchilvers\Utility\File;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

/**
 * App service provider
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $container)
    {
        // Logger
        $container->share(LoggerInterface::class, function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $loggerSettings = $settings['logger'];
            $logger = new Logger('default');
            if (isset($loggerSettings['filename']) && false !== $loggerSettings['filename']) {
                if (DIRECTORY_SEPARATOR !== substr($loggerSettings['filename'], 0, 1)) {
                    $loggerSettings['filename'] = File::join(
                        __DIR__,
                        '/../',
                        $loggerSettings['filename']
                    );
                }
                $logger->pushHandler(
                    new StreamHandler(
                        $loggerSettings['filename'],
                        Logger::DEBUG
                    )
                );
            }
            Registry::addLogger($logger);

            return $logger;
        });

        // Twig
        $container->share(Twig::class, function (ContainerInterface $c) {
            $settings = $c->get('settings')['twig'];
            $cache = $settings['cache'];
            if (false !== $cache && DIRECTORY_SEPARATOR !== substr($cache, 0, 1)) {
                $cache = File::join(
                    __DIR__,
                    '/../',
                    $cache
                );
            }
            $view = new Twig(
                $settings['templates'],
                [
                    'cache' => $cache
                ]
            );
            $request = $c->get('request');
            $basePath = rtrim(str_ireplace('index.php', '', $request->getUri()->getBasePath()), '/');
            $view->addExtension(
                new TwigExtension(
                    $c->get('router'),
                    $basePath
                )
            );

            return $view;
        });

        // Session
        $container->share('session.storage.options', function ($c) {
            return $c->get('settings')['session'];
        });
        $container->share('session.storage', function ($c) {
            $options = $c->get('session.storage.options');

            return new CookieStorage(
                $options
            );
        });
        $container->share('session', function ($c) {
            return new Session(
                $c->get('session.storage')
            );
        });

        // Database
        $container->share(PDO::class, function ($c) {
            $settings = $c->get('settings')['database'];
            return new PDO(
                $settings['dsn'],
                $settings['username'],
                $settings['password'],
                $settings['options']
            );
        });
    }
}
