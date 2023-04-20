<?php

namespace App\Output;

interface OutputInterface
{
    public function print(array $data, string $filename):void;
}