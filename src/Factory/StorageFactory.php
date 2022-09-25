<?php declare(strict_types=1);

namespace App\Factory;

use League\Flysystem\Filesystem;

interface StorageFactory
{
    public static function createStorage(): Filesystem;
}
