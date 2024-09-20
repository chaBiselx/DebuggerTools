<?php

declare(strict_types=1);

namespace Debuggertools\Converter;

class RessourceValueConverter
{

    private $activeConvertion = true;

    public function __construct($activeConvertion = true)
    {
        $this->activeConvertion = $activeConvertion;
    }

    /**
     * Converts memory usage to a readable format.
     *
     * @param int $memoryInBytes Memory usage in bytes.
     * @return string Memory usage in a readable format.
     */
    public function convertToString(float $memoryInBytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $sign = ($memoryInBytes >= 0) ? 1 : -1;
        $memoryInBytes = abs($memoryInBytes);

        $i = 0;
        if ($this->activeConvertion) {
            for ($i = 0; $memoryInBytes >= 1024 && $i < count($units) - 1; $i++) {
                $memoryInBytes /= 1024;
            }
        }

        return round($sign * $memoryInBytes, 2) . ' ' . $units[$i];
    }
}
