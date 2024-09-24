<?php

declare(strict_types=1);

namespace Debuggertools\Escaper;

use Debuggertools\Interfaces\EscaperInterfaces;

class ShellArgEscaper implements EscaperInterfaces
{
    private $incrementValue = 2000;

    /**
     * {@inheritdoc}
     */
    public function escape(string $logMessage): string
    {
        $length = mb_strlen($logMessage);
        $position = 0;

        $newMessage = '';
        while ($position < $length) {
            $endOfString = $position + $this->incrementValue;
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
