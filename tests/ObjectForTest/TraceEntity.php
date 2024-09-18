<?php

namespace Test\ObjectForTest;

use Debuggertools\Trace;

class TraceEntity
{
    public function traceInPublic(): void
    {
        Trace::getTraceStatic();
    }

    public function traceInPrivate(): void
    {
        self::functionPrivate();
    }

    private function functionPrivate()
    {
        Trace::getTraceStatic();
    }

    public function traceInStatic(): void
    {
        self::functionStatic();
    }

    public static function functionStatic()
    {
        Trace::getTraceStatic();
    }

    public function traceInClosure(): void
    {
        $closure = function () {
            Trace::getTraceStatic();
        };
        $closure();
    }
}
