<?php

use App\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Dotenv\Dotenv;

/** @psalm-suppress MissingFile */
require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$doctrine = $kernel->getContainer()->get('doctrine');
if ($doctrine instanceof ManagerRegistry) {
    return $doctrine->getManager();
}

