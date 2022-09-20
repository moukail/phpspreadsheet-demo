<?php

namespace App\Factory;

use League\Flysystem\FilesystemOperator;

interface Storage
{
    public static function createStorage(): FilesystemOperator;
}