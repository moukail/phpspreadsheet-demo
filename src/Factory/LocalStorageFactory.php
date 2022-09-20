<?php

namespace App\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;

class LocalStorageFactory implements Storage
{
    public static function createStorage(): FilesystemOperator
    {
        $rootPath = './data';
        $adapter = new LocalFilesystemAdapter($rootPath);

        return new Filesystem($adapter);
    }
}
