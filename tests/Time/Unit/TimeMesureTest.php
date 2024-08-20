<?php

namespace Test\Time\Unit;


use Debuggertools\Time;
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
    private $coefLogger = 2.2; // 2 standard deviation for 200 log

    /**
     * time in second
     *
     * @var float
     */
    private $additialTimeForLoggerMesure = 0.0088; // avg for 200 log


    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
    }

    public function testBase()
    {
        $Time = new Time();
        $Time->log();

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTime = (float) $match[1];
        $this->assertEquals(0, $mesuredTime);
    }

    public function testControleSimple()
    {
        $Time = new Time();
        $Time->set('label');
        usleep(10);
        $Time->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTime = (float) $match[1];

        $espectedValue = 0.0001;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTime && $mesuredTime <= $upperValue,
            "$lowerValue <= $mesuredTime <= $upperValue"
        );
    }

    public function testControleSimpleBigger()
    {
        $Time = new Time();
        $Time->set('label');
        usleep(5000); // 5 Millisecond
        $Time->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTime = (float) $match[1];

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTime && $mesuredTime <=  $upperValue,
            "$lowerValue <= $mesuredTime <= $upperValue"
        );
    }

    public function testTimeLogger()
    {
        $Logger = new Logger();
        $Time = new Time();
        $Time->set('label');
        $Logger->logger('text');
        $Time->log('label');

        $match = [];
        preg_match('/ => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $mesuredTime = (float) $match[1];

        $nbOLogger = 1;
        $espectedValue = 0;
        $marginOFError = $espectedValue * $this->percentMarginError +  ($nbOLogger * $this->coefLogger * $this->additialTimeForLoggerMesure);
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $mesuredTime && $mesuredTime <=  $upperValue,
            "$lowerValue <= $mesuredTime <= $upperValue"
        );
    }

    public function testMultiLabel()
    {
        $Time = new Time();
        $Time->set('total');
        $Time->set('partial1');
        usleep(5000); // 5 Millisecond
        $Time->log('partial1');
        $Time->set('partial2');
        usleep(5000); // 5 Millisecond
        $Time->log('partial2');
        $Time->log('total');


        $match = [];
        preg_match('/: partial1 => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $partial1MesuredTime = (float) $match[1];
        $match = [];
        preg_match('/: partial2 => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $partial2MesuredTime = (float) $match[1];
        $match = [];
        preg_match('/: total => (\d(.\d+)?) Sec/', $this->getContent(), $match);
        $totalMesuredTime = (float) $match[1];

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $partial1MesuredTime && $partial1MesuredTime <=  $upperValue,
            "partial1 $lowerValue <= $partial1MesuredTime <= $upperValue"
        );

        $espectedValue = 0.005;
        $marginOFError = $espectedValue * $this->percentMarginError;
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $partial2MesuredTime && $partial2MesuredTime <=  $upperValue,
            "partial2 $lowerValue <= $partial2MesuredTime <= $upperValue"
        );

        $nbOLogger = 2;

        $espectedValue = 0.01;
        $marginOFError = $espectedValue * $this->percentMarginError +  ($nbOLogger * $this->coefLogger * $this->additialTimeForLoggerMesure);
        $lowerValue = $espectedValue - $marginOFError;
        $upperValue = $espectedValue + $marginOFError;
        $this->assertTrue(
            $lowerValue <= $totalMesuredTime && $totalMesuredTime <=  $upperValue,
            "total $lowerValue <= $totalMesuredTime <= $upperValue"
        );
    }
}
