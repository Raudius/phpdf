<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;
use function raudius\phpdf\qpdf;

/**
 * Removes certain pages from the PDF.
 * You can specify the range of pages you would like to keep.
 */
class Trim extends Operation {
    /** @var string */
    private $range;

    /**
     * Specify the pages we would like to keep in the document.
     * @param string $range
     */
    public function __construct(string $range) {
        $this->range = $range;
    }

    /**
     * @param Phpdf $pdf PDF file to which the output is saved.
     * @throws PhpdfException
     */
    protected function perform(Phpdf $pdf): void {
        qpdf($pdf->getPath(), '--pages', $pdf->getPath(), $this->range, '--', '--replace-input');
    }
}
