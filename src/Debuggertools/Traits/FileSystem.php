<?php

declare(strict_types=1);

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


    /**
     * Append log to file
     *
     * @param string $pathLog
     * @param string $logMessage
     * @return void
     */
    protected function appendToFile(string $pathLog, string $logMessage): void
    {
        $logMessage = $this->escapeShellArgs($logMessage);
        system("echo $logMessage >> " . $pathLog);
    }


    /**
     * Decomposition to process error messages below
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

            $newMessage .= escapeshellarg($this->cleanSpecialChar(mb_substr($logMessage, $position, $endOfString)));
            $position = $endOfString;
        }

        return $newMessage;
    }

    /**
     * Clean special char which can break the writing of the log file
     *
     * @param string $stringCuted
     * @return string
     */
    private function cleanSpecialChar(string $stringCuted): string
    {
        $specialChars = [
            "\0" => "[\\\\0]",
            "\x01" => "[\\\\x01]",
            "\x02" => "[\\\\x02]",
            "\x03" => "[\\\\x03]",
            "\x04" => "[\\\\x04]",
            "\x05" => "[\\\\x05]",
            "\x06" => "[\\\\x06]",
            "\x07" => "[\\\\x07]",
            "\x08" => "[\\\\x08]",
            "\x0B" => "[\\\\x0B]",
            "\x0C" => "[\\\\x0C]",
            "\x0E" => "[\\\\x0E]",
            "\x0F" => "[\\\\x0F]",
            "\x10" => "[\\\\x10]",
            "\x11" => "[\\\\x11]",
            "\x12" => "[\\\\x12]",
            "\x13" => "[\\\\x13]",
            "\x14" => "[\\\\x14]",
            "\x15" => "[\\\\x15]",
            "\x16" => "[\\\\x16]",
            "\x17" => "[\\\\x17]",
            "\x18" => "[\\\\x18]",
            "\x19" => "[\\\\x19]",
            "\x1A" => "[\\\\x1A]",
            "\x1B" => "[\\\\x1B]",
            "\x1C" => "[\\\\x1C]",
            "\x1D" => "[\\\\x1D]",
            "\x1E" => "[\\\\x1E]",
            "\x1F" => "[\\\\x1F]",
            "\x7F" => "[\\\\x7F]",
        ];

        return strtr($stringCuted, $specialChars);
    }
}
