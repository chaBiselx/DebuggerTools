<?php

namespace Test\MemoryMonitor\Unit;

use Debuggertools\MemoryMonitor;
use Test\ExtendClass\BaseTestCase;
use Debuggertools\Enumerations\OptionForInstanceEnum;

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
        $MemoryMonitor = new MemoryMonitor([OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE => true]);
        $string = str_repeat('A', $this->memoryValue); //NOSONAR
        $MemoryMonitor->logMemoryUsage();
        $match = [];
        preg_match('/ From start (\d*(.\d*)?) .?B /', $this->getContent(), $match);
        $mesuredResource = (int) $match[1];
        $this->assertEqualsWithDelta($this->memoryValue + $this->diffGarbageCollector, $mesuredResource, 1024); // delta from garbagColler stored in MemoryMonitor
    }

    public function testBaseDataSecond()
    {
        $MemoryMonitor = new MemoryMonitor([OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE => true]);
        $MemoryMonitor->logMemoryUsage();
        $string = str_repeat('A', $this->memoryValue); //NOSONAR
        $MemoryMonitor->logMemoryUsage();

        $match = [];
        preg_match('/ From start (\d*(.\d*)?) .?B /', $this->getLastLine(), $match);
        $mesuredResource = (float) $match[1];
        $this->assertEqualsWithDelta($this->memoryValue + $this->diffGarbageCollector, $mesuredResource, 1024); // delta from garbagColler stored in MemoryMonitor
    }
}
