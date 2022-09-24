<?php

namespace App\Factory;

use League\Flysystem\Filesystem;

interface Storage
{
    public static function createStorage(): Filesystem;
}
