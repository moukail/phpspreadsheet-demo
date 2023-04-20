<?php

namespace App\Output;

use Twig\Environment;

class HtmlOutput implements OutputInterface
{
    public function __construct(private Environment $twig)
    {}
    public function print(array $data, string $filename): void
    {
        $html = $this->twig->render($filename. '.html.twig', ['data' => $data]);

        file_put_contents(dirname(__DIR__) . '/../var/output/'.$filename.'.html', $html);
    }
}