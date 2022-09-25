<?php

namespace App\Pdf;

use Mpdf\Mpdf;

class MpdfRenderer implements PdfRenderer
{
    public function __construct(private Mpdf $pdf)
    {
    }

    public function output(string $html)
    {
        $this->pdf->WriteHTML($html);
        return $this->pdf->OutputBinaryData();
    }
}