<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php81\Rector\Class_\SpatieEnumClassToEnumRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->ruleWithConfiguration(ConsistentPregDelimiterRector::class, [
        ConsistentPregDelimiterRector::DELIMITER => '/',
    ]);

    $rectorConfig->skip([
        JsonThrowOnErrorRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        AddLiteralSeparatorToNumberRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
        PostIncDecToPreIncDecRector::class,
        EncapsedStringsToSprintfRector::class,
        RemoveUnusedVariableAssignRector::class => [
            __DIR__ . '/tests',
        ],
        SpatieEnumClassToEnumRector::class,
    ]);
};
