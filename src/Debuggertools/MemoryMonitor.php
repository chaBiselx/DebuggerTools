<?php

declare(strict_types=1);

namespace Debuggertools;

use Debuggertools\Logger;


/**
 * Class to monitor memory usage.
 */
class MemoryMonitor
{
    private $initialMemory;
    private $lastMemory = null;
    private $logger;
    private $activeConvertion = true;
    private $totalMemoryLimit;

    /**
     * Constructor
     *
     */
    public function __construct(array $Option = [])
    {
        $this->logger = new Logger($Option);
        $this->totalMemoryLimit = $this->decodeMemoryLimit(ini_get('memory_limit'));

        if (isset($Option['activeConvertion']) && $Option['activeConvertion']) {
            $this->activeConvertion = true;
        }
        if (isset($Option['disactiveConvertion']) && $Option['disactiveConvertion']) {
            $this->activeConvertion = false;
        }
        $this->initialMemory = memory_get_usage();
    }

    /**
     * Logs current memory usage.
     *
     * @param string $label
     * @return void
     */
    public function logMemoryUsage(string $label = ""): void
    {
        $currentMemory = memory_get_usage();
        $usedMemoryPercentage = ($currentMemory / $this->totalMemoryLimit) * 100;
        $prefixLabel = ($label) ? "$label : " : "";

        $messageToLog = $prefixLabel . $this->convertMemoryUsage($currentMemory) . ' of ' . $this->convertMemoryUsage($this->totalMemoryLimit) . ' (' . $usedMemoryPercentage . '%) ';
        if (!is_null($this->lastMemory)) {
            $usedMemoryEvol = $currentMemory - $this->lastMemory;
            $usedMemoryPercentageEvol = ($usedMemoryEvol / $this->totalMemoryLimit) * 100;
            $messageToLog .= '| From last mesure ' . $this->convertMemoryUsage($usedMemoryEvol) . ' (' . $usedMemoryPercentageEvol . '%) ';
        }
        if (!is_null($this->initialMemory)) {
            $usedMemoryStart = $currentMemory - $this->initialMemory;
            $usedMemoryPercentageStart = ($usedMemoryStart / $this->totalMemoryLimit) * 100;
            $messageToLog .= '| From start ' . $this->convertMemoryUsage($usedMemoryStart) . ' (' . $usedMemoryPercentageStart . '%) ';
        }


        $this->logger->logger($messageToLog);
        $this->lastMemory = $currentMemory;
    }

    /**
     * Converts memory usage to a readable format.
     *
     * @param int $memoryInBytes Memory usage in bytes.
     * @return string Memory usage in a readable format.
     */
    private function convertMemoryUsage($memoryInBytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $i = 0;
        if ($this->activeConvertion) {
            for ($i = 0; $memoryInBytes >= 1024 && $i < count($units) - 1; $i++) {
                $memoryInBytes /= 1024;
            }
        }

        return round($memoryInBytes, 2) . ' ' . $units[$i];
    }

    private function decodeMemoryLimit(string $memoryLimit): float
    {
        $val = trim($memoryLimit);
        $num = (float) preg_replace('/\D*(\s)*$/', '', $val);
        $last = strtoupper(trim(str_replace(strval($num), '', $val)));

        switch ($last) {
                // The 'G' modifier is available
            case 'TB':
            case 'T':
                $num = $num * 1024 * 1024 * 1024 * 1024;
                break;
            case 'GB':
            case 'G':
                $num = $num * 1024 * 1024 * 1024;
                break;
            case 'MB':
            case 'M':
                $num = $num * 1024 * 1024;
                break;
            case 'KB':
            case 'K':
                $num *= 1024;
                break;
            case 'B':
            default:
                #nothing
        }

        return $num;
    }
}
