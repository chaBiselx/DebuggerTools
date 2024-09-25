<?php

namespace Test\MemoryMonitor\Unit;

use Debuggertools\Enumerations\OptionForInstanceEnum;
use Debuggertools\MemoryMonitor;
use Test\ExtendClass\BaseTestCase;

class SimpleTest extends BaseTestCase
{
    private $memoryLimit = '2048MB';

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        ini_set('memory_limit', $this->memoryLimit);
    }

    public function testBase()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/-?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)$/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
    }

    public function testBaseWithLabel()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage("label");
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/label : -?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)$/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
    }

    public function testDoubleLog()
    {
        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage("before");
        $MemoryMonitor->logMemoryUsage("after");
        $this->assertMatchesRegularExpression('/before : -?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
        $this->assertMatchesRegularExpression('/after : -?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From last mesure -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)$/', $this->getContent()); // after : 4.3 MB of 1 KB (440671.875%) | From last mesure 12.77 KB (1277.34375%)| From start 16.63 KB (1663.28125%)\n
    }

    public function testDisactiveConvertion()
    {
        $MemoryMonitor = new MemoryMonitor([OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE => 1]);
        $MemoryMonitor->logMemoryUsage("before");
        $MemoryMonitor->logMemoryUsage("after");
        $this->assertMatchesRegularExpression('/before : -?\d*(.\d*)? B of -?\d*(.\d*)? B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)/', $this->getContent()); // before : 4500048 B of 2048 B (219728.90625%) | From start 3952 B (192.96875%) 
        $this->assertMatchesRegularExpression('/after : -?\d*(.\d*)? B of -?\d*(.\d*)? B \(-?\d+(.\d*)?(E-\d+)?%\) \| From last mesure -?\d*(.\d*)? B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)$/', $this->getContent()); // after : 4500080 B of 2048 B (219730.46875%) | From last mesure 32 B (1.5625%) | From start 3984 B (194.53125%) 
    }

    public function testActiveConvertion()
    {
        $MemoryMonitor = new MemoryMonitor([OptionForInstanceEnum::MEMORY_CONVERTION_ACTIVE => 1]);
        $MemoryMonitor->logMemoryUsage("before");
        $MemoryMonitor->logMemoryUsage("after");
        $this->assertMatchesRegularExpression('/before : -?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)/', $this->getContent()); // 4.27 MB of 1 KB (437525%) | From start 3.86 KB (385.9375%)\n
        $this->assertMatchesRegularExpression('/after : -?\d*(.\d*)? .?B of -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From last mesure -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \| From start -?\d*(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\)(\s*)$/', $this->getContent()); // after : 4.3 MB of 1 KB (440671.875%) | From last mesure 12.77 KB (1277.34375%)| From start 16.63 KB (1663.28125%)\n
    }

    public function testControlDataMemoryLimit()
    {
        ini_set('memory_limit', '2048MB');

        $MemoryMonitor = new MemoryMonitor();
        $MemoryMonitor->logMemoryUsage("memory");

        $this->assertMatchesRegularExpression('/memory : -?\d*(.\d*)? .?B of \d(.\d*)? .?B \(-?\d+(.\d*)?(E-\d+)?%\) \|/', $this->getContent()); // 4.27 MB of 2048 MB (437525%) | From start 3.86 KB (385.9375%)\n
        $matches = [];
        preg_match('/memory : -?\d*(.\d*)? .?B of (\d(.\d*)? .?B) \(-?\d+(.\d*)?(E-\d+)?%\) \|/',  $this->getContent(), $matches);
        $memoryLimit = preg_replace('/\s/', '', $matches[2]);

        $this->assertEquals('2GB', $memoryLimit);
    }
}
