<?php

namespace App\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

class FtpStorageFactory implements Storage
{
    public static function createStorage(): Filesystem
    {
            $options = FtpConnectionOptions::fromArray([
                'host' => $_ENV['FTP_HOST'], // required
                'port' => intval($_ENV['FTP_PORT']),
                'root' => $_ENV['FTP_ROOT'], // required
                'username' => $_ENV['FTP_USERNAME'], // required
                'password' => $_ENV['FTP_PASSWORD'], // required
            ]);

            return new Filesystem(new FtpAdapter($options));
    }
}
