<?php

declare(strict_types=1);

namespace Debuggertools\ExtendClass;


abstract class AbstractAdvancedExtracter
{
    protected $class = '';
    protected $content = '';
    protected $type = '';
    protected $appendLog = [];

    public function __construct() {}

    public function getClass()
    {
        return $this->class;
    }

    public function gettype()
    {
        return $this->type;
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
