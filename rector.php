<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\Class_\ReturnTypeFromStrictTernaryRector;
use Rector\TypeDeclaration\Rector\ClassMethod\BoolReturnTypeFromBooleanStrictReturnsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictReturnsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictScalarReturnsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictConstantReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictFluentReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);

    $rectorConfig->skip([
        __DIR__ . '/src/Migrations'
    ]);

    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');

    $rectorConfig->importNames();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83
    ]);

    $rectorConfig->sets([
        SymfonySetList::SYMFONY_70,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);

    $rectorConfig->sets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES
    ]);

    $rectorConfig->rules([
        ReturnTypeFromStrictTernaryRector::class,
        ReturnTypeFromStrictNewArrayRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        NumericReturnTypeFromStrictReturnsRector::class,
        NumericReturnTypeFromStrictScalarReturnsRector::class,
        BoolReturnTypeFromBooleanStrictReturnsRector::class,
        ReturnTypeFromStrictTypedPropertyRector::class,
        ReturnTypeFromStrictFluentReturnRector::class,
        ReturnTypeFromStrictConstantReturnRector::class,
        ReturnTypeFromStrictTypedCallRector::class
    ]);

};
