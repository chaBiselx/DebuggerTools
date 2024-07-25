<?php

namespace Debuggertools;


class Trace
{

    public function __construct(array $Option = [])
    {
        $Option['hidePrefix'] = true;
        $this->CustomLog = new CustomLog($Option);
        $this->TraceDecoder = new TraceDecoder();
    }

    /**
     * get trace
     *
     * @return void
     */
    public function getTrace(): void
    {
        try {
            $texts = $this->TraceDecoder->decode();
            // write log
            foreach ($texts as $text) {
                $this->CustomLog->logger($text);
            }
        } catch (\Throwable $th) {
            $this->CustomLog->logger(["CUSTOMLOG : an unexpected error has occurred", $th->getMessage(), $th->getTraceAsString()]);
        }
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
