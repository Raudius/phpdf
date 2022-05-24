<?php

namespace raudius\phpdf\Operations;

use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;
use function raudius\phpdf\qpdf;

/**
 * Overlays a PDF onto another one, can be used for adding watermarks.
 * If the overlay PDF has more than 1 page each page will be overlayed in sequence.
 * Unless specified the overlay PDF will be repeated over the whole of the input PDF.
 */
class Overlay extends Operation {
    /** @var Phpdf */
    private $overlayPdf;
    /** @var bool */
    private $repeat;

    /**
     * @param Phpdf $overlayPdf - The pdf file that will be overlayed (the watermark)
     * @param bool $repeat - Whether the overlay should repeat otherwise overlaying stops after we run out of overlayPdf pages
     */
    public function __construct(Phpdf $overlayPdf, bool $repeat=true) {
        $this->overlayPdf = $overlayPdf;
        $this->repeat = $repeat;
    }

    /**
     * @param Phpdf $pdf
     * @throws PhpdfException
     */
    protected function perform(Phpdf $pdf): void {
        $options = [$pdf->getPath(), '--overlay', $this->overlayPdf->getPath()];
        if ($this->repeat) {
            $options[] = "--repeat=1-z";
        }
        $options[] = '--';
        $options[] = '--replace-input';

        qpdf(...$options);
    }
}
