<?php

namespace Debuggertools\Enumerations;

abstract class OptionForInstanceEnum
{
    //general
    const FILE_NAME = 'fileName';
    const EXPEND_OBJECT = 'expendObject';
    const PREFIX_HIDE = 'hidePrefix';
    const PREFIX_SHOW = 'showPrefix';
    const ACTIVE_PURGE_FILE = 'purgeFileBefore';

    //MemoryMonitor
    const MEMORY_CONVERTION_ACTIVE = 'activeConvertion';
    const MEMORY_CONVERTION_DISACTIVE = 'disactiveConvertion';

    const LIST_PARAM = [
        self::EXPEND_OBJECT => true,
        self::PREFIX_HIDE => false,
        self::PREFIX_SHOW => true,
        self::MEMORY_CONVERTION_ACTIVE => true,
        self::MEMORY_CONVERTION_DISACTIVE => false,
    ];
}
