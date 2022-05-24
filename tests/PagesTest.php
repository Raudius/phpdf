<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;

class PagesTest extends TestCase {
    /**
     * Test trimming the 10-page test file with valid trimming ranges.
     * @dataProvider providerOrientationFilter
     */
    public function testOrientationFilter(string $filePath, string $orientation, array $expectedResult): void {
        $pdf = Phpdf::fopen($filePath);
        $result = getPagesByOrientation($pdf, $orientation);
        $this->assertEqualsCanonicalizing($expectedResult, $result);
    }

    public function testEdgeCases(): void {
        $pdf = Phpdf::fopen('test_files/password.pdf');
        $this->assertEmpty(getPagesByOrientation($pdf, ORIENTATION_PORTRAIT));
        $this->assertEmpty(getPagesByOrientation($pdf, ORIENTATION_LANDSCAPE));

        $pdf = Phpdf::fopen('test_files/normal.pdf');
        $this->assertEmpty(getPagesByOrientation($pdf, 'abc'));
    }

    public function providerOrientationFilter(): array {
        return [
            [ 'test_files/orientations.pdf', ORIENTATION_PORTRAIT, [1,2,3,4] ],
            [ 'test_files/orientations.pdf', ORIENTATION_LANDSCAPE, [5,6,7] ]
        ];
    }
}
