<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;

/**
 * Blueprint class for modifying PDF files.
 */
abstract class Operation {
    /**
     * Performs an operation on a PDF (e.g. rotates all the pages 90 degrees).
     * Writes the resulting pdf to the file provided in the argumente
     *
     * @param Phpdf $pdf
     * @throws PhpdfException
     */
    abstract protected function perform(Phpdf $pdf): void;

    /**
     * @param Phpdf|null $pdf
     * @return Phpdf
     * @throws PhpdfException
     */
    public function execute(Phpdf $pdf = null): Phpdf {
        $pdf = $pdf ?? Phpdf::create();
        $this->perform($pdf);
        return $pdf;
    }
}
