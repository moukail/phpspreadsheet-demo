<?php declare(strict_types=1);

namespace App\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class LocalStorageFactory implements StorageFactory
{
    public static function createStorage(): Filesystem
    {
        $rootPath = './data';
        $adapter = new LocalFilesystemAdapter($rootPath);

        return new Filesystem($adapter);
    }
}
