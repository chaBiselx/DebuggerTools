<?php

declare(strict_types=1);

namespace Debuggertools;

use Debuggertools\Logger;
use Debuggertools\Objects\TraceInterpret;

class Trace
{

    public function __construct(array $Option = [])
    {
        $Option['hidePrefix'] = true;
        $this->Logger = new Logger($Option);
        $this->TraceInterpret = new TraceInterpret();
    }

    /**
     * get logTrace
     *
     * @return void
     */
    public function logTrace(): void
    {
        try {
            $texts = $this->TraceInterpret->decode();
            // write log
            foreach ($texts as $text) {
                $this->Logger->logger($text);
            }
        } catch (\Throwable $th) {
            $this->Logger->logger(["LOGGER : an unexpected error has occurred", $th->getMessage(), $th->getTraceAsString()]);
        }
    }

    /**
     * static getTraceStatic
     *
     * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data , same data as logger
     * @param  $Option , same data as constructor
     *
     * @return void
     */
    public static function getTraceStatic($Option = []): void
    {
        (new Trace($Option))->logTrace();
    }
}
