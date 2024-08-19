<?php

namespace Debuggertools;

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
        if (isset($Option['fileName']) && $Option['fileName']) {
            $this->fileName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $Option['fileName']);
            $this->setPathFile();
        }
        if (isset($Option['expendObject']) && $Option['expendObject']) { // expend object / array
            $this->expendObject = true;
        }
        if (isset($Option['hidePrefix']) && $Option['hidePrefix']) { // hide prefix
            $this->showPrefix = false;
        }
        if (isset($Option['showPrefix']) && $Option['showPrefix']) { // show prefix
            $this->showPrefix = true;
        }

        if (isset($Option['purgeFileBefore']) && $Option['purgeFileBefore']) { //reset file
            file_put_contents($this->pathFile, '');
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
