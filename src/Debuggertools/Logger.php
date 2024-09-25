<?php

declare(strict_types=1);

namespace Debuggertools;

use Debuggertools\Enumerations\OptionForInstanceEnum;
use Debuggertools\ExtendClass\AbstractCustomLog;

class Logger extends AbstractCustomLog
{


    /**
     * list param for Options
     * @var bool hideDate Hide the date at the beginning of the string
     * @var bool purgeFileBefore purge the file before write
     * @var bool expendObject expend for best visibility in log file
     * @var string fileName write in the file with the same name default: log
     *
     */
    public function __construct(array $Option = [])
    {
        parent::__construct(); // default value

        //Option
        if (isset($Option[OptionForInstanceEnum::FILE_NAME]) && $Option[OptionForInstanceEnum::FILE_NAME]) {
            $this->fileName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $Option[OptionForInstanceEnum::FILE_NAME]);
            $this->setPathFile();
        }
        if (isset($Option[OptionForInstanceEnum::EXPEND_OBJECT]) && $Option[OptionForInstanceEnum::EXPEND_OBJECT]) { // expend object / array
            $this->expendObject = OptionForInstanceEnum::LIST_PARAM[OptionForInstanceEnum::EXPEND_OBJECT];
        }
        if (isset($Option[OptionForInstanceEnum::PREFIX_HIDE]) && $Option[OptionForInstanceEnum::PREFIX_HIDE]) { // hide prefix
            $this->showPrefix = OptionForInstanceEnum::LIST_PARAM[OptionForInstanceEnum::PREFIX_HIDE];
        }
        if (isset($Option[OptionForInstanceEnum::PREFIX_SHOW]) && $Option[OptionForInstanceEnum::PREFIX_SHOW]) { // show prefix
            $this->showPrefix = OptionForInstanceEnum::LIST_PARAM[OptionForInstanceEnum::PREFIX_SHOW];
        }

        if (isset($Option[OptionForInstanceEnum::ACTIVE_PURGE_FILE]) && $Option[OptionForInstanceEnum::ACTIVE_PURGE_FILE]) { //reset file
            $this->purgeFile();
        }
    }

    /**
     * logger
     *
     * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data
     *
     * @return void
     */
    public function logger($data): void
    {
        try {
            $texts = $this->generateTextFormData($data);
            // write log
            $this->writeInLog($texts);
        } catch (\Throwable $th) {
            $this->writeInLog(["LOGGER : an unexpected error has occurred", $th->getMessage(), $th->getTraceAsString()]);
        }
    }

    /**
     * static logger
     *
     * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data , same data as logger
     * @param  $Option , same data as constructor
     *
     * @return void
     */
    public static function loggerStatic($data, array $Option = []): void
    {
        (new Logger($Option))->logger($data);
    }
}
