<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;
use function raudius\phpdf\getPageCount;
use function raudius\phpdf\qpdf;

/**
 * Combines multiple PDFs into one.
 */
class Merge extends Operation {
    /** @var Phpdf[] */
    private $pdfs;

    /**
     * Note if a PDF is passed in the @see execute call, it will be used as the first PDF in the merge.
     * @param Phpdf[] $pdfs PDFs to be merged
     */
    public function __construct(array $pdfs) {
        $this->pdfs = $pdfs;
    }

    /**
     * @param Phpdf $pdf PDF file to which the output is saved.
     * @throws PhpdfException
     */
    protected function perform(Phpdf $pdf): void {
        $options = [$pdf->getPath(), '--pages'];

        $mergePdfs = $this->pdfs;
        $mergablePdfs = 0;
        array_unshift($mergePdfs, $pdf);
        foreach ($mergePdfs as $mergePdf) {
            if (getPageCount($mergePdf) <= 0) {
                continue;
            }
            $mergablePdfs++;
            $options[] = $mergePdf->getPath();
        }

        if ($mergablePdfs === 0) {
            return;
        }

        $options[] = '--';
        $options[] = '--replace-input';
        qpdf(...$options);
    }
}
