<?php

namespace Test\MemoryMonitor\Unit;


use Debuggertools\MemoryMonitor;
use Test\ExtendClass\BaseTestCase;

class SimpleTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        ini_set('memory_limit', '2048MB');
    }

    public function testBase()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/\d*(.\d*)? .?B of \d(.\d*)? .?B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? .?B \(\d+(.\d*)?%\)(\s*)$/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
    }

    public function testBaseWithLabel()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage("label");
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/label : \d*(.\d*)? .?B of \d(.\d*)? .?B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? .?B \(\d+(.\d*)?%\)(\s*)$/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
    }

    public function testDoubleLog()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage("before");
        $MemoryMonitor->logMemoryUsage("after");
        $this->assertMatchesRegularExpression('/before : \d*(.\d*)? .?B of \d(.\d*)? .?B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? .?B \(\d+(.\d*)?%\)(\s*)/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
        $this->assertMatchesRegularExpression('/after : \d*(.\d*)? .?B of \d(.\d*)? .?B \(\d+(.\d*)?%\) \| From last mesure \d*(.\d*)? .?B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? .?B \(\d+(.\d*)?%\)(\s*)$/', $this->getContent()); // after : 4.3 MB of 1 KB (440671.875%) | From last mesure 12.77 KB (1277.34375%)| From start 16.63 KB (1663.28125%)\n
    }

    public function testDisabledConvertion()
    {
        $MemoryMonitor = new MemoryMonitor(['disactiveConvertion' => 1]);
        $MemoryMonitor->logMemoryUsage("before");
        $MemoryMonitor->logMemoryUsage("after");
        $this->assertMatchesRegularExpression('/before : \d*(.\d*)? B of \d(.\d*)? B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? B \(\d+(.\d*)?%\)(\s*)/', $this->getContent()); // before : 4500048 B of 2048 B (219728.90625%) | From start 3952 B (192.96875%) 
        $this->assertMatchesRegularExpression('/after : \d*(.\d*)? B of \d(.\d*)? B \(\d+(.\d*)?%\) \| From last mesure \d*(.\d*)? B \(\d+(.\d*)?%\) \| From start \d*(.\d*)? B \(\d+(.\d*)?%\)(\s*)$/', $this->getContent()); // after : 4500080 B of 2048 B (219730.46875%) | From last mesure 32 B (1.5625%) | From start 3984 B (194.53125%) 
    }
}
