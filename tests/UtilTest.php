<?php

namespace raudius\phpdf;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase {
    public function testGetDocData() {
        $this->assertIsArray(getDocData(Phpdf::fopen('test_files/null.pdf')));
        $this->assertNull(getDocData(Phpdf::fopen('test_files/password.pdf')));
        $this->assertNull(getDocData(Phpdf::fopen('test_files/notapdf.txt')));
    }

    public function testCheckQpdfDependency() {
        $this->assertTrue(checkQpdfDependency());
    }
}
