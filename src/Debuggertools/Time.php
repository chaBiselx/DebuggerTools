<?php

namespace Debuggertools;


class Time
{

    /**
     * @var LoggerInterface Logger instance for logging times
     */
    private $Logger;

    /**
     * @var array Stores the start time for each label
     */
    private $timeLogger = [];

    private $timeFromConstruct;
    private $resultTimePrecision = 4;

    /**
     * list param for Options
     * @var bool hideDate Hide the date at the beginning of the string
     * @var bool purgeFileBefore purge the file before write
     * @var bool expendObject expend for best visibility in log file
     * @var string fileName write in the file with the same name default: log
     *
     */
    public function __construct(array $Option = [])
    {
        //Option
        $this->Logger = new Logger($Option);
        $this->timeFromConstruct =  $this->generateMicroTime();
    }


    /**
     * Undocumented function
     *
     * @param string|null $label
     * @return void
     */
    public function log(?string $label = null): void
    {
        if (!isset($this->timeLogger[$label])) {
            $time = $this->timeFromConstruct;
            $label .= " from __construct";
        } else {
            $time = $this->timeLogger[$label];
        }
        $resultTime = round($this->generateMicroTime() - $time, $this->resultTimePrecision);
        $this->Logger->logger(trim($label) . " => $resultTime Sec");
    }

    public function set(string $label = 'time')
    {
        $this->setTime($label);
    }

    private function setTime($label)
    {
        $this->timeLogger[$label] = $this->generateMicroTime();
    }

    /**
     * Undocumented function
     *
     * @return float
     */
    private function generateMicroTime(): float
    {
        return microtime(true);
    }
}
