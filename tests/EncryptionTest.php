<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;
use raudius\phpdf\Operations\Decrypt;

class EncryptionTest extends TestCase {
    /**
     * @param string $filePath
     * @param bool $isEncrypted
     * @param string $password
     * @return void
     * @throws PhpdfException
     * @dataProvider providerEncryptionTest
     */
    public function testEncryption(string $filePath, bool $isEncrypted, string $password='') {
        $pdf = Phpdf::fopen($filePath);
        $this->assertEquals($isEncrypted, isEncrypted($pdf));

        (new Decrypt($password))->execute($pdf);
        $this->assertFalse(isEncrypted($pdf));
    }

    public function providerEncryptionTest(): array {
        return [
            [ 'test_files/restricted.pdf', true ],
            [ 'test_files/password.pdf', true, '12345' ],
            [ 'test_files/null.pdf', false ],
            [ 'test_files/normal.pdf', false ]
        ];
    }
}
