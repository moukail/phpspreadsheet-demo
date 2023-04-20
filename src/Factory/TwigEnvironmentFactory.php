<?php

namespace App\Factory;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigEnvironmentFactory
{
    public static function createEnvironment()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/../templates');
        return new Environment($loader, [
            'debug' => $_ENV['APP_DEBUG'] ?? false,
            'cache' => dirname(__DIR__) . '/../var/cache/twig',
        ]);
    }
}