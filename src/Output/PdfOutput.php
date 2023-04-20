<?php

namespace App\Output;

use App\Pdf\PdfRenderer;
use Twig\Environment;

class PdfOutput implements OutputInterface
{
    public function __construct(private Environment $twig, private PdfRenderer $pdf)
    {}
    public function print(array $data, string $filename): void
    {
        $html = $this->twig->render($filename. '.html.twig', ['data' => $data]);
        $pdf = $this->pdf->output($html);

        file_put_contents(dirname(__DIR__) . '/../var/output/'.$filename.'.pdf', $pdf);
    }
}