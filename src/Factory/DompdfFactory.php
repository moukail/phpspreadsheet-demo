<?php

namespace App\Factory;

use App\Pdf\DomPdfRenderer;
use App\Pdf\PdfRenderer;
use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfFactory implements PdfFactory
{
    public static function createPdfRenderer(): PdfRenderer
    {
        $pdfOptions = new Options();
        $pdfOptions
            ->set('defaultFont', 'Arial')
            ->setIsJavascriptEnabled(true)
            ->setIsRemoteEnabled(true)
            //->setDebugPng(true)
        ;

        return new DomPdfRenderer(new Dompdf($pdfOptions));
    }
}