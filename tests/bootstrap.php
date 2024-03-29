<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$kernel = new \App\Kernel($_SERVER['APP_ENV'], $_SERVER['APP_DEBUG']);
$kernel->boot();

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->setAutoExit(false);

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:drop',
    '--if-exists' => '1',
    '--force' => '1',
]));
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:create'
]));
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:schema:create'
]));
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'app:setup'
]));

$kernel->shutdown();