<?php

namespace App\Factory;

use App\Pdf\PdfRenderer;

interface PdfFactory
{
    public static function createPdfRenderer(): PdfRenderer;
}