<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;

class SimpleTypeTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testString()
    {
        $this->Logger->logger("i'm a big string");
        $this->assertMatchesRegularExpression('/i\'m a big string(\s*)$/', $this->getContent());
    }

    public function testBooleanTrue()
    {
        $this->Logger->logger(true);
        $this->assertMatchesRegularExpression('/boolean : true(\s*)$/', $this->getContent());
    }

    public function testBooleanFalse()
    {
        $this->Logger->logger(false);
        $this->assertMatchesRegularExpression('/boolean : false(\s*)$/', $this->getContent());
    }

    public function testInt()
    {
        $this->Logger->logger(15454);
        $this->assertMatchesRegularExpression('/15454(\s*)$/', $this->getContent());
    }

    public function testFloat()
    {
        $this->Logger->logger(1.1546554);
        $this->assertMatchesRegularExpression('/1.1546554(\s*)$/', $this->getContent());
    }

    public function testNull()
    {
        $this->Logger->logger(null);
        $this->assertMatchesRegularExpression('/NULL$/', $this->getContent());
    }
}
