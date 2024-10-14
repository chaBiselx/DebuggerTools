<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\Enum\CardEnum;

class EnumTest extends BaseTestCase
{

    public function setUp(): void
    {
        if (floatval(PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION) < 8.1) {
            $this->markTestSkipped('Enum doesn\'t exist in PHP < 8');
        }
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testGeneral()
    {
        $enumeration = CardEnum::Clubs;
        $this->Logger->logger($enumeration);
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/ : UnitEnum \'Test\\\\ObjectForTest\\\\Enum\\\\CardEnum\' : /', $this->getContent());
    }

    public function testGetInfo()
    {
        $enumeration = CardEnum::Clubs;
        $this->Logger->logger($enumeration);
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/ : \{"name":"Clubs","functions":\["color","shape","cases"\]\}/', $this->getContent());
    }
}
