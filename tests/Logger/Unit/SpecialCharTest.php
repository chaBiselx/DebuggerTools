<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;

class SpecialCharTest extends BaseTestCase
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

    public function testBackSlashInString()
    {
        $this->Logger->logger("hello \\ backslash");
        $this->assertMatchesRegularExpression('/hello \\\\ backslash$/', $this->getContent());
    }

    public function testSlashInString()
    {
        $this->Logger->logger("hello / slash");
        $this->assertMatchesRegularExpression('/hello \\/ slash$/', $this->getContent());
    }

    public function testQuotes()
    {
        $this->Logger->logger("i'm a \"big\" and `tall` ");
        $this->assertMatchesRegularExpression('/i\'m a \"big\" and \`tall\` $/', $this->getContent());
    }

    public function testControlCharx00()
    {
        $this->Logger->logger("hello hell \0 bytes");
        $this->assertMatchesRegularExpression('/hello hell \[\\\\0\] bytes$/', $this->getContent(), 'Null byte');
    }

    public function testControlCharx00_2()
    {
        $this->Logger->logger("i \x00 control");
        $this->assertMatchesRegularExpression('/i \[\\\\0\] control$/', $this->getContent(), 'Null byte');
    }

    public function testControlCharx01()
    {
        $this->Logger->logger("i \x01 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x01\] control$/', $this->getContent(), 'Start of heading');
    }

    public function testControlCharx02()
    {
        $this->Logger->logger("i \x02 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x02\] control$/', $this->getContent(), 'Start of text');
    }

    public function testControlCharx03()
    {
        $this->Logger->logger("i \x03 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x03\] control$/', $this->getContent(), 'End of text');
    }

    public function testControlCharx04()
    {
        $this->Logger->logger("i \x04 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x04\] control$/', $this->getContent(), 'End of transmission');
    }

    public function testControlCharx05()
    {
        $this->Logger->logger("i \x05 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x05\] control$/', $this->getContent(), 'Enquiry');
    }

    public function testControlCharx06()
    {
        $this->Logger->logger("i \x06 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x06\] control$/', $this->getContent(), 'Acknowledge');
    }

    public function testControlCharx07()
    {
        $this->Logger->logger("i \x07 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x07\] control$/', $this->getContent(), 'Ring terminal bell');
    }

    public function testControlCharx08()
    {
        $this->Logger->logger("i \x08 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x08\] control$/', $this->getContent(), 'Backspace');
    }

    public function testControlCharx09()
    {
        $this->Logger->logger("i \x09 control");
        $this->assertMatchesRegularExpression('/i 	 control$/', $this->getContent(), 'Horizontal tab');
    }

    public function testHorizontalTab()
    {
        $this->Logger->logger("i 	 control");
        $this->assertMatchesRegularExpression('/i 	 control$/', $this->getContent());
    }

    public function testControlCharx0A()
    {
        $this->Logger->logger("i'm a line \x0A and a new line");
        $this->assertMatchesRegularExpression('/i\'m a line \n and a new line$/', $this->getContent(), 'Line feed');
    }

    public function testLineFeed()
    {
        $this->Logger->logger("i'm a line \n and a new line");
        $this->assertMatchesRegularExpression('/i\'m a line \n and a new line$/', $this->getContent());
    }

    public function testControlCharx0B()
    {
        $this->Logger->logger("i \x0B control");
        $this->assertMatchesRegularExpression('/i \[\\\\x0B\] control$/', $this->getContent(), 'Form feed');
    }

    public function testControlCharx0C()
    {
        $this->Logger->logger("i \x0C control");
        $this->assertMatchesRegularExpression('/i \[\\\\x0C\] control$/', $this->getContent(), 'Form feed');
    }

    public function testControlCharx0D()
    {
        $this->Logger->logger("i'm a line \x0D and a new line");
        $this->assertMatchesRegularExpression('/i\'m a line \r and a new line$/', $this->getContent(), 'Carriage return');
    }

    public function testCarriageReturn()
    {
        $this->Logger->logger("i'm a line \r and a new line");
        $this->assertMatchesRegularExpression('/i\'m a line \r and a new line$/', $this->getContent());
    }

    public function testControlCharx0E()
    {
        $this->Logger->logger("i \x0E control");
        $this->assertMatchesRegularExpression('/i \[\\\\x0E\] control$/', $this->getContent(), 'Shift out');
    }

    public function testControlCharx0F()
    {
        $this->Logger->logger("i \x0F control");
        $this->assertMatchesRegularExpression('/i \[\\\\x0F\] control$/', $this->getContent(), 'Shift in');
    }

    public function testControlCharx10()
    {
        $this->Logger->logger("i \x10 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x10\] control$/', $this->getContent(), 'Data link escape');
    }

    public function testControlCharx11()
    {
        $this->Logger->logger("i \x11 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x11\] control$/', $this->getContent(), 'Device control 1');
    }

    public function testControlCharx12()
    {
        $this->Logger->logger("i \x12 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x12\] control$/', $this->getContent(), 'Device control 2');
    }

    public function testControlCharx13()
    {
        $this->Logger->logger("i \x13 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x13\] control$/', $this->getContent(), 'Device control 3');
    }

    public function testControlCharx14()
    {
        $this->Logger->logger("i \x14 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x14\] control$/', $this->getContent(), 'Device control 4');
    }

    public function testControlCharx15()
    {
        $this->Logger->logger("i \x15 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x15\] control$/', $this->getContent(), 'Negative acknowledge');
    }

    public function testControlCharx16()
    {
        $this->Logger->logger("i \x16 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x16\] control$/', $this->getContent(), 'Synchronous idle');
    }

    public function testControlCharx17()
    {
        $this->Logger->logger("i \x17 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x17\] control$/', $this->getContent(), 'End of transmission block');
    }

    public function testControlCharx18()
    {
        $this->Logger->logger("i \x18 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x18\] control$/', $this->getContent(), 'Cancel');
    }

    public function testControlCharx19()
    {
        $this->Logger->logger("i \x19 control");
        $this->assertMatchesRegularExpression('/i \[\\\\x19\] control$/', $this->getContent(), 'End of medium');
    }

    public function testControlCharx1A()
    {
        $this->Logger->logger("i \x1A control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1A\] control$/', $this->getContent(), 'Substitute character');
    }

    public function testControlCharx1B()
    {
        $this->Logger->logger("i \x1B control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1B\] control$/', $this->getContent(), 'Escape');
    }

    public function testControlCharx1C()
    {
        $this->Logger->logger("i \x1C control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1C\] control$/', $this->getContent(), 'File separator, Information separator four');
    }

    public function testControlCharx1D()
    {
        $this->Logger->logger("i \x1D control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1D\] control$/', $this->getContent(), 'Group separator, Information separator three');
    }

    public function testControlCharx1E()
    {
        $this->Logger->logger("i \x1E control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1E\] control$/', $this->getContent(), 'Record separator, Information separator two');
    }

    public function testControlCharx1F()
    {
        $this->Logger->logger("i \x1F control");
        $this->assertMatchesRegularExpression('/i \[\\\\x1F\] control$/', $this->getContent(), 'Unit separator, Information separator one');
    }

    public function testControlCharx7F()
    {
        $this->Logger->logger("i \x7F control");
        $this->assertMatchesRegularExpression('/i \[\\\\x7F\] control$/', $this->getContent(), 'Delete');
    }
}
