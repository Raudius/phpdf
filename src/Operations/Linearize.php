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
    /** @var string|null */
    private $password;

    /**
     * Optionally a password can be provided to unlock the file if the file is password protected.
     *
     * @param string|null $password
     */
    public function __construct(string $password = null) {
        $this->password = $password;
    }

    /**
     * @param Phpdf $pdf
     * @throws PhpdfException
     */
    protected function perform(Phpdf $pdf): void {
        qpdf($pdf->getPath(), '--linearize', '--', '--replace-input');
    }
}
