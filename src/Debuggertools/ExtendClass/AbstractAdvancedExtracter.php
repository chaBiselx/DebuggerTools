<?php

namespace Debuggertools\ExtendClass;


abstract class AbstractAdvancedExtracter
{
    protected $class = null;
    protected $content = null;
    protected $appendLog = [];

    public function __construct() {}

    public function getClass()
    {
        return $this->class;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAppendedLog()
    {
        return $this->appendLog;
    }
}
