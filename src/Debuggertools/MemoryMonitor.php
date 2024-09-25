<?php

declare(strict_types=1);

namespace Debuggertools;

use Debuggertools\Logger;
use Debuggertools\Converter\RessourceValueConverter;
use Debuggertools\Enumerations\OptionForInstanceEnum;

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

        if (isset($Option[OptionForInstanceEnum::MEMORY_CONVERTION_ACTIVE]) && $Option[OptionForInstanceEnum::MEMORY_CONVERTION_ACTIVE]) {
            $this->activeConvertion = OptionForInstanceEnum::LIST_PARAM[OptionForInstanceEnum::MEMORY_CONVERTION_ACTIVE];
        }
        if (isset($Option[OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE]) && $Option[OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE]) {
            $this->activeConvertion = OptionForInstanceEnum::LIST_PARAM[OptionForInstanceEnum::MEMORY_CONVERTION_DISACTIVE];
        }
        $this->RessourceValueConverter = new RessourceValueConverter($this->activeConvertion);
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

        $messageToLog = $prefixLabel . $this->RessourceValueConverter->convertToString($currentMemory) . ' of ' . $this->RessourceValueConverter->convertToString($this->totalMemoryLimit) . ' (' . $usedMemoryPercentage . '%) ';
        if (!is_null($this->lastMemory)) {
            $usedMemoryEvol = $currentMemory - $this->lastMemory;
            $usedMemoryPercentageEvol = ($usedMemoryEvol / $this->totalMemoryLimit) * 100;
            $messageToLog .= '| From last mesure ' . $this->RessourceValueConverter->convertToString($usedMemoryEvol) . ' (' . $usedMemoryPercentageEvol . '%) ';
        }
        if (!is_null($this->initialMemory)) {
            $usedMemoryStart = $currentMemory - $this->initialMemory;
            $usedMemoryPercentageStart = ($usedMemoryStart / $this->totalMemoryLimit) * 100;
            $messageToLog .= '| From start ' . $this->RessourceValueConverter->convertToString($usedMemoryStart) . ' (' . $usedMemoryPercentageStart . '%) ';
        }


        $this->logger->logger($messageToLog);
        $this->lastMemory = $currentMemory;
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
