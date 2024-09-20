<?php

namespace Debuggertools\Strategy;

use Debuggertools\Converter\TypeConverter;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\Exceptions\FunctionalException;

class DataExtractorContext
{
    private $extracter;

    public function __construct(ExtracterInterface $extracter, bool $expendObject)
    {
        $this->extracter = $extracter;
        $this->expendObject = $expendObject;
        $this->typeConverter = new TypeConverter();
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
                $texts[0] .= $this->createExpendedJson($content);
            }

            $appendLog = $this->extracter->getAppendedLog();
            if (isset($appendLog) && count($appendLog)) {
                $texts = array_merge($texts, $appendLog);
            }
        } catch (\Throwable $th) {
            throw new FunctionalException("Error extracting data from $type : " . $th->getMessage(), 1);
        }
    }

    /**
     * Create a json if necessery
     *
     * @param mixed $data
     * @param boolean $expendObject if true expend the object
     * @param integer $nbSpace
     * @return string
     */
    protected  function createExpendedJson($data, $nbSpace = 0): string
    {
        $stringResponse = '';
        $type = gettype($data);
        //base indent
        $indent = self::createIndent($nbSpace);

        if (in_array($type, ['object', 'array'])) {
            if ($this->expendObject) {
                $stringResponse .= $indent . "\n";
                if (
                    $type == 'object' ||
                    $this->hasStringKeys($data)
                ) {
                    $srtCroche = '{';
                    $endCroche = '}';
                } else {
                    $srtCroche = '[';
                    $endCroche = ']';
                }

                $stringResponse .= $indent . $srtCroche . "\n";
                foreach ($data as $key => $subData) {
                    $stringResponse .= $indent . "  " . $key . " : ";
                    $stringResponse .= $this->createExpendedJson($subData, $nbSpace + 2) . "\n";
                }
                $stringResponse .= $indent . $endCroche;
            } else {
                $stringResponse = $indent . json_encode($data);
            }
        } else {
            $stringResponse = $this->typeConverter->convertArgToString($data);
        }
        return $stringResponse;
    }


    /**
     * check if the array associative or not
     *
     * @param array $array
     * @return boolean
     */
    protected  function hasStringKeys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * Create base to indentation
     *
     * @param integer $nbSpace
     * @return string
     */
    protected static function createIndent(int $nbSpace): string
    {
        $indent = "";
        for ($i = 0; $i < $nbSpace; $i++) {
            $indent .= " ";
        }
        return $indent;
    }
}
