<?php

namespace Test\MemoryMonitor\Unit;


use Debuggertools\MemoryMonitor;
use Test\ExtendClass\BaseTestCase;

class ResourceMesureTest extends BaseTestCase
{
    private $memoryLimit = '2048MB';
    protected $memoryValue;

    public function setUp(): void
    {
        parent::setUp();
        $this->memoryValue = 100 * 1024;
        $this->purgeLog();
        ini_set('memory_limit', $this->memoryLimit);

        $init = memory_get_usage();
        $string = str_repeat('A',  $this->memoryValue); //NOSONAR
        $end = memory_get_usage();
        $this->diffGarbageCollector = $end - $init -  $this->memoryValue;
    }

    public function testBaseData()
    {
        $MemoryMonitor = new MemoryMonitor(['disactiveConvertion' => true]);
        $string = str_repeat('A', $this->memoryValue); //NOSONAR
        $MemoryMonitor->logMemoryUsage();
        $match = [];
        preg_match('/ From start (\d*(.\d*)?) .?B /', $this->getContent(), $match);
        $mesuredResource = (int) $match[1];
        $this->assertEquals($this->memoryValue + $this->diffGarbageCollector, $mesuredResource);
    }

    public function testBaseDataSecond()
    {
        $MemoryMonitor = new MemoryMonitor(['disactiveConvertion' => true]);
        $MemoryMonitor->logMemoryUsage();
        $string = str_repeat('A', $this->memoryValue); //NOSONAR
        $MemoryMonitor->logMemoryUsage();

        $match = [];
        preg_match('/ From start (\d*(.\d*)?) .?B /', $this->getLastLine(), $match);
        $mesuredResource = (float) $match[1];
        $this->assertEqualsWithDelta($this->memoryValue + $this->diffGarbageCollector, $mesuredResource, 420); // delta from garbagColler stored in MemoryMonitor
    }
}
