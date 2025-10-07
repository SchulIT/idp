<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

passthru(
    sprintf(
        'APP_ENV=%s php "%s/../bin/console" doctrine:database:drop --if-exists --force',
        $_ENV['APP_ENV'],
        __DIR__
    )
);

passthru(
    sprintf(
        'APP_ENV=%s php "%s/../bin/console" doctrine:database:create',
        $_ENV['APP_ENV'],
        __DIR__
    )
);

passthru(
    sprintf(
        'APP_ENV=%s php "%s/../bin/console" doctrine:schema:create',
        $_ENV['APP_ENV'],
        __DIR__
    )
);


passthru(
    sprintf(
        'APP_ENV=%s php "%s/../bin/console" app:setup',
        $_ENV['APP_ENV'],
        __DIR__
    )
);
