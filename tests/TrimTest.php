<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;
use raudius\phpdf\Operations\Trim;

class TrimTest extends TestCase {
    /**
     * Test trimming the 10-page test file with valid trimming ranges.
     * @dataProvider providerValidTrimRanges
     */
    public function testValidTrims(string $range, string $expetedOutputPageCount): void {
        $pdf = Phpdf::fopen('test_files/10pages.pdf');
        (new Trim($range))->execute($pdf);

        self::assertEquals($expetedOutputPageCount, getPageCount($pdf));
    }

    public function providerValidTrimRanges(): array {
        return [
            [ '1-5', 5 ],
            [ '5-1', 5 ],
            [ 'r1-r5', 5 ],
            [ '1-z', 10 ],
            [ '5-z', 6 ],
            [ '', 10 ],
            [ '1,3,5,9', 4 ],
            [ '1,8-10', 4 ],
            [ '1,10,4-8:odd', 4 ],
            [ '1-z:even', 5 ],
            [ '1-z:even', 5 ],
        ];
    }

    /**
     * Test trimming the 10-page test file with invalid trimming ranges.
     * @dataProvider providerInvalidTrimRanges
     */
    public function testInvalidTrims(string $range, string $errorMsg) {
        $pdf = Phpdf::fopen('test_files/10pages.pdf');

        $this->expectException(PhpdfException::class);
        $this->expectExceptionMessage($errorMsg);
        (new Trim($range))->execute($pdf);
    }

    public function providerInvalidTrimRanges(): array {
        return [
            [ '1-100', 'error in numeric range 1-100: number 100 out of range' ],
            [ '100-1', 'error in numeric range 100-1: number 100 out of range' ],
            [ 'ABCD', 'error at * in numeric range *ABCD: unexpected character' ],
        ];
    }
}
