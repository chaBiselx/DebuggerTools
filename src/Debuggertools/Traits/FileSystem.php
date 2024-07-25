<?php

namespace Debuggertools\Traits;

trait FileSystem
{
    /**
     * Create a dir if not exist
     *
     * @param string $path
     * @return void
     */
    protected function createDirIfNotExist(string $path, int $Permission = 0777): void
    {
        if (!file_exists($path)) {
            mkdir($path, $Permission, true);
        }
    }


    protected function appendToFile(string $pathLog, string $logMessage): void
    {
        $logMessage = $this->escapeShellArgs($logMessage);
        system("echo $logMessage >> " . $pathLog);
    }


    /**
     * Decomposition to process error messages below
     * Fatal error: escapeshellarg(): Argument exceeds the allowed length of 2097152 bytes
     *
     * @param string $logMessage
     * @return string
     */
    private function  escapeShellArgs(string $logMessage): string
    {
        $length = mb_strlen($logMessage);
        $position = 0;
        $incrementValue = 1000;

        $newMessage = '';
        while ($position < $length) {
            $endOfString = $position + $incrementValue;

            $newMessage .= escapeshellarg(mb_substr($logMessage, $position, $endOfString));
            $position = $endOfString;
        }

        return $newMessage;
    }
}
