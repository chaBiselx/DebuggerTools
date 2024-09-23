<?php

namespace Debuggertools\Strategy;

use Debuggertools\Config\InstanceConfig;
use Debuggertools\Converter\TypeConverter;
use Debuggertools\Formatter\JSONformatter;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\Exceptions\FunctionalException;

class DataExtractorContext
{
    private $extracter;

    public function __construct(ExtracterInterface $extracter)
    {
        $this->extracter = $extracter;
        $this->typeConverter = new TypeConverter();
        $this->JSONformatter = new JSONformatter();
    }

    public function extractData(&$texts, $data, string $type): void
    {
        try {
            $this->extracter->extract($data);
            $class = $this->extracter->getClass();

            $texts[0] = $this->extracter->gettype();
            if ($class) {
                $texts[0] .= " '$class' : ";
            }
            $content = $this->extracter->getContent();
            if (!is_null($content)) {
                $texts[0] .= $this->JSONformatter->createExpendedJson($content);
            }

            $appendLog = $this->extracter->getAppendedLog();
            if (isset($appendLog) && count($appendLog)) {
                $texts = array_merge($texts, $appendLog);
            }
        } catch (\Throwable $th) {
            throw new FunctionalException("Error extracting data from $type : " . $th->getMessage(), 1);
        }
    }
}
