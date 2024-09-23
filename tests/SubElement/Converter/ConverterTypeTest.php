<?php

namespace Test\TimeMonitor\Unit;


use Test\ExtendClass\BaseTestCase;
use Debuggertools\Converter\TypeConverter;
use Test\ObjectForTest\UserEntity;

class ConverterTypeTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->TypeConverter = new TypeConverter();
    }


    public function testConvertString()
    {
        $returnType = $this->TypeConverter->convertArgToString('string');
        $this->assertEquals('string', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertInt()
    {

        $returnType =  $this->TypeConverter->convertArgToString(1526);
        $this->assertEquals(1526, $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertFloat()
    {

        $returnType =  $this->TypeConverter->convertArgToString(0.2156);
        $this->assertEquals(0.2156, $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertNull()
    {

        $returnType =  $this->TypeConverter->convertArgToString(null);
        $this->assertEquals('NULL', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertBooleanTrue()
    {

        $returnType =  $this->TypeConverter->convertArgToString(true);
        $this->assertEquals('true', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertBooleanFalse()
    {

        $returnType =  $this->TypeConverter->convertArgToString(false);
        $this->assertEquals('false', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertObject()
    {

        $returnType =  $this->TypeConverter->convertArgToString(new UserEntity());
        $this->assertEquals('\'Test\ObjectForTest\UserEntity\'', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }

    public function testConvertClosure()
    {

        $returnType =  $this->TypeConverter->convertArgToString(function () {
            return 'closure';
        });
        $this->assertEquals('\'Closure\'', $returnType);
        $this->assertEquals('string', gettype($returnType));
    }
}
