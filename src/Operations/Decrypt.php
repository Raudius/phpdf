<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;
use function raudius\phpdf\qpdf;

/**
 * Removes encryption from a PDF file.
 */
class Decrypt extends Operation {
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
        $options = ['--decrypt', $pdf->getPath()];

        if ($this->password) {
            $options[] = "--password={$this->password}";
        }
        $options[] = '--';
        $options[] = '--replace-input';

        qpdf(...$options);
    }
}
