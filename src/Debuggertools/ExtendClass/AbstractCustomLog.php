<?php

declare(strict_types=1);

namespace Debuggertools\ExtendClass;

use Debuggertools\Config\PathLog;
use Debuggertools\Traits\FileSystem;
use Debuggertools\Objects\ClassDecoder;
use Debuggertools\Config\Configurations;
use Debuggertools\Converter\TypeConverter;
use Debuggertools\Extractor\ClassExtracter;
use Debuggertools\Extractor\ResourceExtracter;
use Debuggertools\Exceptions\FunctionalException;

abstract class AbstractCustomLog
{
    use FileSystem;

    /**
     * fileName
     *
     * @var string
     */
    protected $folderPath = '';

    /**
     * fileName
     *
     * @var string
     */
    protected $fileName = 'log';

    /**
     * fileExtension
     *
     * @var string
     */
    protected $fileExtension = 'log';

    /**
     * expendObject
     *
     * @var boolean
     */
    protected $expendObject = false;

    /**
     * showPrefix
     *
     * @var boolean
     */
    protected $showPrefix = true;

    /**
     * config
     *
     * @var array
     */
    protected $config = [];

    /**
     * pathFile
     *
     * @var string|null
     */
    protected  $pathFile = null;

    public function __construct()
    {
        $this->folderPath = (new PathLog())->getLogFolderPath();
        $this->setDefaultWithConfig();
        $this->createDirIfNotExist($this->folderPath); // FileSystem


        $this->ClassDecoder = new ClassDecoder();
        $this->ResourceExtracter = new ResourceExtracter();
        $this->ClassExtracter = new ClassExtracter();
        $this->typeConverter = new TypeConverter();
    }

    private function setDefaultWithConfig()
    {
        $this->config = (new Configurations)->getConfig();
        //file
        $this->fileName = $this->config['fileLog']['defaultName'] ?? 'log';
        $this->fileExtension = $this->config['fileLog']['defaultExtension'] ?? 'log';
        //bool
        $this->showPrefix = $this->config['prefixLog']['defaultShowPrefix'] ?? true;
        $this->setPathFile();
    }

    protected function setPathFile()
    {
        $this->pathFile = $this->folderPath . DIRECTORY_SEPARATOR . $this->fileName . "." . $this->fileExtension;
    }

    /**
     * Generate an array of text for the logger
     *
     * @param $data
     * @return array
     */
    protected function generateTextFormData($data): array
    {
        //check type and get contennt
        $type = gettype($data);
        $texts = [];
        switch ($type) {
            case 'object':
                $this->extratDataObject($texts, $data);
                break;
            case 'array':
                $texts[0] = $type . " : " . $this->decodeArrayForLog($data);
                break;
            case 'resource':
                $texts[0] = $type;
                $this->extratDataResource($texts, $data);
                break;
            case 'integer':
            case 'float':
            case 'double':
            case 'real':
            case 'string':
            case 'boolean':
                $texts[0] = $this->typeConverter->convertArgToString($data);
                break;

            default:
                $texts[0] = $type;
                break;
        }
        return $texts;
    }

    protected function extratDataObject(&$texts, $data): void
    {
        $type = 'object';
        if (is_object($data)) { // class or complexe object
            $texts[0] = $type;
            try {
                $this->ClassExtracter->extract($data);
                $texts[0] = "class '" . $this->ClassExtracter->getClass() . "' : "; //write type
                $texts[0] .= $this->createExpendedJson($this->ClassExtracter->getContent()); //Write content of the object
                $appendLog = $this->ClassExtracter->getAppendedLog();
                if (isset($appendLog) && count($appendLog)) {
                    $texts = array_merge($texts, $appendLog); // write more informations
                }
            } catch (\Throwable $th) {
                throw new FunctionalException("Error extract data from $type", 1);
            }
        } else { // simple object
            $texts[0] = $type . " : " . $this->createExpendedJson($data);
        }
    }

    protected function extratDataResource(&$texts, $data): void
    {
        $type = 'resource';
        try {
            $this->ResourceExtracter->extract($data);
            $class = $this->ResourceExtracter->getClass();
            if ($class) {
                $texts[0] = $type . " '$class' : "; //write type
                $texts[0] .= $this->createExpendedJson($this->ResourceExtracter->getContent()); //Write content of the object
                $appendLog = $this->ResourceExtracter->getAppendedLog();
                if (isset($appendLog) && count($appendLog)) {
                    $texts = array_merge($texts, $appendLog); // write more informations
                }
            }
        } catch (\Throwable $th) {
            throw new FunctionalException("Error extract data from $type", 1);
        }
    }

    /**
     * Write in logs file
     *
     * @param array $ArrayOfText
     * @return void
     */
    protected  function writeInLog(array $ArrayOfText)
    {
        if (!$this->pathFile) throw new \Error("Unknown file");
        $this->createMissingDirectories();
        if (!file_exists($this->pathFile)) touch($this->pathFile);
        $dateFormat = $this->config['prefixLog']['date']['format'];
        $separator = $this->config['prefixLog']['date']['separator'];

        $prefixText = date($dateFormat) . $separator;
        foreach ($ArrayOfText as $text) {
            if ($this->showPrefix) {
                $text = $prefixText . $text;
            }
            $this->appendToFile($this->pathFile, $text);
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
     * Decode a liste of objects
     *
     * @param mixed $arrayofObject
     * @return array
     */
    protected  function decodeListObjet($arrayofObject): array
    {
        $fakeData = [];

        foreach ($arrayofObject as $object) {
            if (gettype($object) == 'object') {
                $this->ClassExtracter->extract($object);
                $fakeData[] = ['class' =>  $this->ClassExtracter->getClass(), 'content' =>  $this->ClassExtracter->getContent()];
            } else {
                $fakeData[] = ['class' => gettype($object), 'content' =>  $object];
            }
        }

        return $fakeData;
    }

    protected function decodeArrayForLog($data): string
    {
        $ret = "";
        if (!empty($data)) {
            if (isset($data[0]) && gettype($data[0]) == "object") {
                $fakeData = $this->decodeListObjet($data);
                $ret = $this->createExpendedJson($fakeData);
            } else {
                $ret = $this->createExpendedJson($data);
            }
        } else {
            $ret = $this->createExpendedJson($data);
        }
        return $ret;
    }
}
