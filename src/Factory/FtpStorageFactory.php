<?php

namespace App\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

class FtpStorageFactory implements Storage
{
    public static function createStorage(): FilesystemOperator
    {
            $options = FtpConnectionOptions::fromArray([
                'host' => 'paragin-sftp', // required
                'port' => 22,
                'root' => '/data', // required
                'username' => 'moukail', // required
                'password' => 'pass_1234', // required
            ]);

            return new Filesystem(new FtpAdapter($options));

    }
}