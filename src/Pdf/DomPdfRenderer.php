<?php

namespace App\Pdf;

use Dompdf\Dompdf;

class DomPdfRenderer implements PdfRenderer
{
    public function __construct(private Dompdf $pdf)
    {
    }

    public function output(string $html)
    {
        $this->pdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $this->pdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $this->pdf->render();
        // Store PDF Binary Data
        return $this->pdf->output();
    }
}