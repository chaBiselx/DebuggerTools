<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\UserEntity;

class ArrayTypeTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testArrayOfString()
    {
        $this->Logger->logger(['string1', 'string2']);
        $this->assertMatchesRegularExpression('/\["string1","string2"\](\s*)$/', $this->getContent());
    }

    public function testArrayOfBoolean()
    {
        $this->Logger->logger([true, false]);
        $this->assertMatchesRegularExpression('/\[true,false\](\s*)$/', $this->getContent());
    }

    public function testArrayOfInt()
    {
        $this->Logger->logger([156, 47]);
        $this->assertMatchesRegularExpression('/\[156,47\](\s*)$/', $this->getContent());
    }

    public function testArrayOfFloat()
    {
        $this->Logger->logger([15.187, 6548.484]);
        $this->assertMatchesRegularExpression('/\[15.187,6548.484\](\s*)$/', $this->getContent());
    }

    public function testArrayObject()
    {
        $UserEntity = new UserEntity();
        $UserEntity->setFirstName('John');
        $UserEntity->setLastName('Doe');
        $this->Logger->logger([$UserEntity]);
        $this->assertMatchesRegularExpression('/array : \[{"class":"Test\\\ObjectForTest\\\UserEntity","content":{"lastName":"Doe","firstName":"John","->getRole":null,"->getId":\d*,"->getLastName":"Doe","->getFirstName":"John"}}\](\s*)$/', $this->getContent());
    }

    public function testArrayOfMixed()
    {
        $this->Logger->logger(['string1', true, 15, 15.7852]);
        $this->assertMatchesRegularExpression('/\["string1",true,15,15.7852\](\s*)$/', $this->getContent());
    }

    public function testIndexArrayBoolTrue()
    {
        $this->Logger->logger(['booleanTrue' => true]);
        $this->assertMatchesRegularExpression('/{"booleanTrue":true}(\s*)$/', $this->getContent());
    }

    public function testIndexArrayBoolFalse()
    {
        $this->Logger->logger(['booleanFalse' => false]);
        $this->assertMatchesRegularExpression('/{"booleanFalse":false}(\s*)$/', $this->getContent());
    }

    public function testIndexArrayString()
    {
        $this->Logger->logger(['string1' => 'string1', 'string2' => 'string2']);
        $this->assertMatchesRegularExpression('/{"string1":"string1","string2":"string2"}(\s*)$/', $this->getContent());
    }

    public function testIndexArrayInt()
    {
        $this->Logger->logger(['float' => 18]);
        $this->assertMatchesRegularExpression('/{"float":18}(\s*)$/', $this->getContent());
    }

    public function testIndexArrayFloat()
    {
        $this->Logger->logger(['float' => 12.15475]);
        $this->assertMatchesRegularExpression('/{"float":12.15475}(\s*)$/', $this->getContent());
    }

    public function testIndexArrayObject()
    {
        $UserEntity = new UserEntity();
        $UserEntity->setFirstName('John');
        $UserEntity->setLastName('Doe');
        $this->Logger->logger(['user' => $UserEntity]);
        $this->assertMatchesRegularExpression('/array : {"user":{"lastName":"Doe","firstName":"John"}}(\s*)$/', $this->getContent());
    }
}
