<?php

namespace Test\Time\Unit;


use Debuggertools\Time;
use Test\ExtendClass\BaseTestCase;

class SimpleTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
    }

    public function testBase()
    {
        $Time = new Time();
        $Time->log();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/ : from __construct => 0 Sec(\s*)$/', $this->getContent());
    }

    public function testLabelFromConstruct()
    {
        $Time = new Time();
        $Time->log('label');
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/ : label from __construct => 0 Sec(\s*)$/', $this->getContent());
    }

    public function testTimeCheck()
    {
        $Time = new Time();
        $Time->set('label');
        usleep(10);
        $Time->log('label');
        $this->assertMatchesRegularExpression('/ : label => (\d(.\d+)?) Sec(\s*)$/', $this->getContent());
    }

    public function testMultiLabel()
    {
        $Time = new Time();
        $Time->set('total');
        $Time->set('partial1');
        usleep(50);
        $Time->log('partial1');
        $Time->set('partial2');
        usleep(50);
        $Time->log('partial2');
        $Time->log('total');
        $this->assertMatchesRegularExpression('/ : partial1 => (\d(.\d+)?) Sec(\s*)/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : partial2 => (\d(.\d+)?) Sec(\s*)/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : total => (\d(.\d+)?) Sec(\s*)$/', $this->getContent());
    }
}
