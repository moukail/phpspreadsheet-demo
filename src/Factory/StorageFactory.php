<?php

namespace App\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Local\LocalFilesystemAdapter;

class StorageFactory
{
    public static function create(string $type)
    {
        $adapter = null;

        if ($type == 'local'){
            $rootPath = './data';
            $adapter = new LocalFilesystemAdapter($rootPath);
        }

        if ($type == 'sftp'){
            $options = FtpConnectionOptions::fromArray([
                'host' => 'paragin-sftp', // required
                'port' => 22,
                'root' => '/data', // required
                'username' => 'moukail', // required
                'password' => 'pass_1234', // required
            ]);
            $adapter = new FtpAdapter($options);

        }

        return new Filesystem($adapter);
    }
}
