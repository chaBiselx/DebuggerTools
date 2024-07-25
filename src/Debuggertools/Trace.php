<?php

namespace Debuggertools;


class Trace
{

    public function __construct(array $Option = [])
    {
        $Option['hidePrefix'] = true;
        $this->CustomLog = new CustomLog($Option);
    }

    /**
     * get trace
     *
     * @return void
     */
    public function getTrace(): void
    {
        try {
            $texts = $this->generateTrace();
            // write log
            foreach ($texts as $text) {
                $this->CustomLog->logger($text);
            }
        } catch (\Throwable $th) {
            $this->CustomLog->logger(["CUSTOMLOG : an unexpected error has occurred", $th->getMessage(), $th->getTraceAsString()]);
        }
    }

    protected function generateTrace(): array
    {
        $traces = debug_backtrace();
        $arrayText = [];
        foreach ($traces as $trace) {
            if (preg_match('/chabiselx\/debuggertools/', $trace['file'])) continue;
            $messageFile = $trace['file'] . ' (line : ' . $trace['line'] . ')';
            $messageFunction = "       -> " .  $trace['function'];
            $messageFunction .= "(";
            if ($trace['args'] && !empty($trace['args'])) {
                foreach ($trace['args'] as $k => $arg) {
                    if ($k != 0) $messageFunction .= ' ,';
                    $messageFunction .= $this->convertArgToString($arg);
                }
            }
            $messageFunction .= ")";

            $arrayText[] = $messageFile;
            $arrayText[] = $messageFunction;
        }
        return $arrayText;
    }

    protected function convertArgToString($arg): string
    {
        $text = "";
        //check type
        $type = gettype($arg);
        switch ($type) {
            case 'integer':
            case 'float':
            case 'double':
            case 'string':
                $text = $arg;
                break;
            case 'boolean':
                $text = $type . ' : ' . ($arg ? 'TRUE' : 'FALSE');
                break;
            case 'object':
            case 'array':
            default:
                $text = $type;
                break;
        }
        return $text;
    }


    /**
     * static getTrace
     *
     * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data , same data as logger
     * @param  $Option , same data as constructor
     *
     * @return void
     */
    public static function getTraceStatic($Option = []): void
    {
        (new Trace($Option))->getTrace();
    }
}
