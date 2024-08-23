<?php

namespace Test\TimeMonitor\Unit;


use Debuggertools\TimeMonitor;
use Debuggertools\Logger;
use Test\ExtendClass\BaseTestCase;

class TimeMesureTest extends BaseTestCase
{
    /**
     * Percent for margin
     *
     * @var [type]
     */
    private $percentMarginError = 5 / 100;

    /**
     * Coeg for write log
     *
     * @var [type]
     */
    private $coefLogger = 2.4; // 2 standard deviation for 200 log

    /**
     * TimeMonitor in second
     *
     * @var float
     */
    private $additialTimeMonitorForLoggerMesure = 0.0088; // avg for 200 log


    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
    }

    public function testBase()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->log();

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTimeMonitor = (float) $match[1];
        $this->assertEquals(0, $mesuredTimeMonitor);
    }

    public function testControleSimple()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('label');
        usleep(10);
        $TimeMonitor->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTimeMonitor = (float) $match[1];

        $espectedValue = 0.0001;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTimeMonitor && $mesuredTimeMonitor <= $upperValue,
            "$lowerValue <= $mesuredTimeMonitor <= $upperValue"
        );
    }

    public function testControleSimpleBigger()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('label');
        usleep(5000); // 5 Millisecond
        $TimeMonitor->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTimeMonitor = (float) $match[1];

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTimeMonitor && $mesuredTimeMonitor <=  $upperValue,
            "$lowerValue <= $mesuredTimeMonitor <= $upperValue"
        );
    }

    public function testTimeMonitorLogger()
    {
        $Logger = new Logger();
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('label');
        $Logger->logger('text');
        $TimeMonitor->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTimeMonitor = (float) $match[1];

        $nbOLogger = 1;
        $espectedValue = 0;
        $marginOFError = $espectedValue * $this->percentMarginError +  ($nbOLogger * $this->coefLogger * $this->additialTimeMonitorForLoggerMesure);
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTimeMonitor && $mesuredTimeMonitor <=  $upperValue,
            "$lowerValue <= $mesuredTimeMonitor <= $upperValue"
        );
    }

    public function testMultiLabel()
    {
        $TimeMonitor = new TimeMonitor();
        $TimeMonitor->set('total');
        $TimeMonitor->set('partial1');
        usleep(5000); // 5 Millisecond
        $TimeMonitor->log('partial1');
        $TimeMonitor->set('partial2');
        usleep(5000); // 5 Millisecond
        $TimeMonitor->log('partial2');
        $TimeMonitor->log('total');


        $match = [];
        preg_match('/: partial1 => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $partial1MesuredTimeMonitor = (float) $match[1];
        $match = [];
        preg_match('/: partial2 => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $partial2MesuredTimeMonitor = (float) $match[1];
        $match = [];
        preg_match('/: total => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $totalMesuredTimeMonitor = (float) $match[1];

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $partial1MesuredTimeMonitor && $partial1MesuredTimeMonitor <=  $upperValue,
            "partial1 $lowerValue <= $partial1MesuredTimeMonitor <= $upperValue"
        );

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $partial2MesuredTimeMonitor && $partial2MesuredTimeMonitor <=  $upperValue,
            "partial2 $lowerValue <= $partial2MesuredTimeMonitor <= $upperValue"
        );

        $nbOLogger = 2;

        $espectedValue = 0.01;
        $marginOFError = $espectedValue * $this->percentMarginError +  ($nbOLogger * $this->coefLogger * $this->additialTimeMonitorForLoggerMesure);
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $totalMesuredTimeMonitor && $totalMesuredTimeMonitor <=  $upperValue,
            "total $lowerValue <= $totalMesuredTimeMonitor <= $upperValue"
        );
    }
}
