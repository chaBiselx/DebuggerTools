<?php

namespace Test\TimeMonitor\Unit;


use Test\ExtendClass\BaseTestCase;
use Debuggertools\Converter\TypeConverter;
use Test\ObjectForTest\UserEntity;

class ConverterTypeTest extends BaseTestCase
{
    public function testConvertStringStatic()
    {
        $returnType = TypeConverter::convertArgToString('string');
        $this->assertEquals('string', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertStringNonStatic()
    {
        $TypeConverter = new TypeConverter();
        $returnType = $TypeConverter->convertArgToString('string');
        $this->assertEquals('string', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertInt()
    {

        $returnType = TypeConverter::convertArgToString(1526);
        $this->assertEquals(1526, $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertFloat()
    {

        $returnType = TypeConverter::convertArgToString(0.2156);
        $this->assertEquals(0.2156, $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertNull()
    {

        $returnType = TypeConverter::convertArgToString(null);
        $this->assertEquals('NULL', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertBooleanTrue()
    {

        $returnType = TypeConverter::convertArgToString(true);
        $this->assertEquals('boolean : TRUE', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertBooleanFalse()
    {

        $returnType = TypeConverter::convertArgToString(false);
        $this->assertEquals('boolean : FALSE', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertObject()
    {

        $returnType = TypeConverter::convertArgToString(new UserEntity());
        $this->assertEquals('\'Test\ObjectForTest\UserEntity\'', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertClosure()
    {

        $returnType = TypeConverter::convertArgToString(function () {
            return 'closure';
        });
        $this->assertEquals('\'Closure\'', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }
}
