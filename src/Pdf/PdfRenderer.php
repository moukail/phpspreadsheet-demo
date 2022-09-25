<?php

namespace App\Pdf;

interface PdfRenderer
{
    public function output(string $html);
}