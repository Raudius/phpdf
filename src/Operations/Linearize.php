<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;
use function raudius\phpdf\qpdf;

/**
 * Linearizes a PDF file.
 * @url https://qpdf.readthedocs.io/en/stable/linearization.html
 */
class Linearize extends Operation {
    /**
     * @param Phpdf $pdf
     * @throws PhpdfException
     */
    protected function perform(Phpdf $pdf): void {
        qpdf($pdf->getPath(), '--linearize', '--', '--replace-input');
    }
}
