<?php

namespace Test\RessourceValueConverter\Unit;


use Test\ExtendClass\BaseTestCase;
use Debuggertools\Converter\RessourceValueConverter;


class ConvertionResourceTest extends BaseTestCase
{
    public function testByte()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1);
        $this->assertEquals("1 B", $result);
    }

    public function testKiloBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024);
        $this->assertEquals("1 KB", $result);
    }

    public function testKiloBytesWithDecimal1()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 + (1024 / 2));
        $this->assertEquals("1.5 KB", $result);
    }

    public function testKiloBytesWithDecimalDecimal2()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 + (1024 / 4));
        $this->assertEquals("1.25 KB", $result);
    }

    public function testKiloBytesWithDecimalInfiniteDecimal2()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 + (1024 / 3));
        $this->assertEquals("1.33 KB", $result);
    }

    public function testKiloBytesWithDecimalInfiniteDecimal1()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 + (2 * 1024 / 3));
        $this->assertEquals("1.67 KB", $result);
    }

    public function testMegaBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 * 1024);
        $this->assertEquals("1 MB", $result);
    }

    public function testGigaBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 * 1024 * 1024);
        $this->assertEquals("1 GB", $result);
    }

    public function testTeraBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(1024 * 1024 * 1024 * 1024);
        $this->assertEquals("1 TB", $result);
    }

    public function testNegativeByte()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(-1);
        $this->assertEquals("-1 B", $result);
    }

    public function testNegativeKiloBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(-1024);
        $this->assertEquals("-1 KB", $result);
    }

    public function testNegativeMegaBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(-1024 * 1024);
        $this->assertEquals("-1 MB", $result);
    }

    public function testNegativeGigaBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(-1024 * 1024 * 1024);
        $this->assertEquals("-1 GB", $result);
    }

    public function testNegativeTeraBytes()
    {
        $RessourceValueConverter = new RessourceValueConverter();
        $result = $RessourceValueConverter->convertToString(-1024 * 1024 * 1024 * 1024);
        $this->assertEquals("-1 TB", $result);
    }
}
