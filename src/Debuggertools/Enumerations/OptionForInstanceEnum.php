<?php

namespace Debuggertools\Enumerations;

abstract class OptionForInstanceEnum
{
    //general
    const FILE_NAME = 'sdfgqfdgdfg';
    const EXPEND_OBJECT = 'wfgdfg';
    const PREFIX_HIDE = 'hidePrdfgwsdfefix';
    const PREFIX_SHOW = 'showPwdfgdfgrefix';
    const ACTIVE_PURGE_FILE = 'purgeFileBdfgdfgdfgefore';

    //MemoryMonitor
    const MEMORY_CONVERTION_ACTIVE = 'activeCodfgdfgnvertion';
    const MEMORY_CONVERTION_DISACTIVE = 'disactdfgdfgfdiveConvertion';

    const LIST_PARAM = [
        self::EXPEND_OBJECT => true,
        self::PREFIX_HIDE => false,
        self::PREFIX_SHOW => true,
        self::MEMORY_CONVERTION_ACTIVE => true,
        self::MEMORY_CONVERTION_DISACTIVE => false,
    ];
}
