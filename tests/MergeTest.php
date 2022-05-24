<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;
use raudius\phpdf\Operations\Merge;

class MergeTest extends TestCase {
    /**
     * @param array $filePaths
     * @return void
     * @throws PhpdfException
     * @dataProvider providerMergeTest
     */
    public function testMerge(array $filePaths, string $baseFilePath = null): void {
        $pdfs = array_map(static function (string $path): Phpdf {
            return Phpdf::fopen($path);
        }, $filePaths);
        $basePdf = $baseFilePath ? Phpdf::fopen($baseFilePath) : null;

        $combinedPageCount = $basePdf ? getPageCount($basePdf) : 0;
        foreach ($pdfs as $pdf) {
            $combinedPageCount += getPageCount($pdf);
        }

        $outPdf = (new Merge($pdfs))->execute($basePdf);

        self::assertEquals($combinedPageCount, getPageCount($outPdf));
    }

    public function providerMergeTest(): array {
        return [
            // Test merging null files
            [ [] ],
            [ [], 'test_files/null.pdf' ],
            [ ['test_files/null.pdf', 'test_files/null.pdf'] ],
            [ ['test_files/null.pdf', 'test_files/null.pdf'], 'test_files/null.pdf' ],

            // Test merging with null files
            [ ['test_files/null.pdf', 'test_files/null.pdf'], 'test_files/10pages.pdf' ],
            [ ['test_files/null.pdf', 'test_files/10pages.pdf'], 'test_files/null.pdf' ],
            [ ['test_files/null.pdf', 'test_files/10pages.pdf', 'test_files/2pages.pdf'], 'test_files/10pages.pdf' ],
            [ ['test_files/null.pdf', 'test_files/10pages.pdf', 'test_files/2pages.pdf'] ],

            // Test merging regular files
            [ ['test_files/10pages.pdf', 'test_files/2pages.pdf'], 'test_files/normal.pdf' ],
            [ ['test_files/10pages.pdf', 'test_files/2pages.pdf'] ],
            [ ['test_files/10pages.pdf'] ],
            [ [], 'test_files/10pages.pdf' ],
        ];
    }

    /**
     * @param string $path
     * @param int $expectedPageCount
     * @return void
     * @dataProvider providerPageCountTest
     */
    public function testPageCount(string $path, int $expectedPageCount, string $password = null): void {
        $n = getPageCount(Phpdf::fopen($path), $password);
        self::assertEquals($expectedPageCount, $n);
    }

    public function providerPageCountTest(): array {
        return [
            [ 'test_files/10pages.pdf', 10 ],
            [ 'test_files/2pages.pdf', 2 ],
            [ 'test_files/normal.pdf', 1 ],
            [ 'test_files/restricted.pdf', 1 ],
            [ 'test_files/password.pdf', -1 ],
            [ 'test_files/password.pdf', 1, '12345' ],
            [ 'test_files/null.pdf', 0 ],
        ];
    }
}
