<?php

namespace Test\TimeMonitor\Unit;


use Debuggertools\TimeMonitor;
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
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->log();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/ : from __construct => 0 Sec(\s*)$/', $this->getContent());
    }

    public function testLabelFromConstruct()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->log('label');
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/ : label from __construct => 0 Sec(\s*)$/', $this->getContent());
    }

    public function testTimeMonitorCheck()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('label');
        usleep(10);
        $TimeMonitor->log('label');
        $this->assertMatchesRegularExpression('/ : label => (\d(.\d+)?) Sec(\s*)$/', $this->getContent());
    }

    public function testMultiLabel()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('total');
        $TimeMonitor->set('partial1');
        usleep(50);
        $TimeMonitor->log('partial1');
        $TimeMonitor->set('partial2');
        usleep(50);
        $TimeMonitor->log('partial2');
        $TimeMonitor->log('total');
        $this->assertMatchesRegularExpression('/ : partial1 => (\d(.\d+)?) Sec(\s*)/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : partial2 => (\d(.\d+)?) Sec(\s*)/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : total => (\d(.\d+)?) Sec(\s*)$/', $this->getContent());
    }
}
