<?php

namespace App\Factory;

use App\Pdf\MpdfRenderer;
use App\Pdf\PdfRenderer;
use Mpdf\Mpdf;

class MpdfFactory implements PdfFactory
{
    public static function createPdfRenderer(): PdfRenderer
    {
        return new MpdfRenderer(new Mpdf());
    }
}