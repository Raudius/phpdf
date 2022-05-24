<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;
use raudius\phpdf\Operations\Overlay;
use raudius\phpdf\Operations\Trim;

class OverlayTest extends TestCase {
    /**
     * Test trimming the 10-page test file with valid trimming ranges.
     * @dataProvider providerOverlay
     */
    public function testOverlay(string $pathOverlayPdf, string $pathTargetPdf): void {
        $overlayPdf = Phpdf::fopen($pathOverlayPdf);
        $targetPdf = Phpdf::fopen($pathTargetPdf);

        $preObjectsCount = count(getDocData($targetPdf)['objects']);
        (new Overlay($overlayPdf))->execute($targetPdf);
        $postObjectsCount = count(getDocData($targetPdf)['objects']);

        $this->assertGreaterThan($preObjectsCount, $postObjectsCount);
    }

    public function providerOverlay(): array {
        return [
            [ 'test_files/restricted.pdf', 'test_files/normal.pdf' ],
            [ 'test_files/normal.pdf', 'test_files/normal.pdf' ],
        ];
    }
}
