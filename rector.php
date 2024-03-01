<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rector): void {
    $rector->parallel();
    $rector->paths([__DIR__.'/src', __DIR__.'/tests']);
    $rector->phpVersion(PhpVersion::PHP_81);
    $rector->phpstanConfig(__DIR__.'/tools/phpstan/phpstan-rector.neon');
    $rector->sets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,
        PHPUnitSetList::PHPUNIT_100,
    ]);
};
