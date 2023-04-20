<?php

namespace App\Output;

use League\CLImate\CLImate;

class ConsoleOutput implements OutputInterface
{
    public function __construct(private CLImate $climate)
    {}

    public function print(array $data, string $filename): void
    {
        $this->climate->table($data);
    }
}