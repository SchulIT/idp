<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
    ->withAttributesSets(
        symfony: true,
        doctrine: true,
        gedmo: true,
        jms: true,
        sensiolabs: true
    )
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        rectorPreset: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true
    );