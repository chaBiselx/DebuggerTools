<?php

declare(strict_types=1);

namespace Debuggertools\ExtendClass;

use Debuggertools\Config\PathLog;
use Debuggertools\Traits\FileSystem;
use Debuggertools\Objects\ClassDecoder;
use Debuggertools\Config\Configurations;
use Debuggertools\Config\InstanceConfig;
use Debuggertools\Converter\TypeConverter;
use Debuggertools\Formatter\JSONformatter;
use Debuggertools\Extractor\ClassExtracter;
use Debuggertools\Extractor\ResourceExtracter;
use Debuggertools\Strategy\DataExtractorContext;

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
        $this->createDirIfNotExist($this->folderPath); // FileSystem

        $this->ClassDecoder = new ClassDecoder();
        $this->ResourceExtracter = new ResourceExtracter();
        $this->ClassExtracter = new ClassExtracter();
        $this->typeConverter = new TypeConverter();
        $this->JSONformatter = new JSONformatter();
        $this->instanceConfig = new InstanceConfig();

        $this->setDefaultWithConfig();
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
        $this->setGlobals();
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

        $this->setGlobals();
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
        $this->instanceConfig->reset();
        return $texts;
    }

    protected function setGlobals()
    {
        $this->instanceConfig->set('showPrefix', $this->showPrefix);
        $this->instanceConfig->set('expendObject', $this->expendObject);
        $this->instanceConfig->set('pathFile', $this->pathFile);
    }


    protected function extratDataObject(&$texts, $data): void
    {
        $extractor = new DataExtractorContext($this->ClassExtracter); // Injecter ClassExtracter
        $extractor->extractData($texts, $data, 'object');
    }

    protected function extratDataResource(&$texts, $data): void
    {
        $extractor = new DataExtractorContext($this->ResourceExtracter); // Injecter ResourceExtracter
        $extractor->extractData($texts, $data, 'resource');
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

    protected function decodeArrayForLog($data): string
    {
        $ret = "";
        if (!empty($data)) {
            if (isset($data[0]) && gettype($data[0]) == "object") {
                $fakeData = $this->decodeListObjet($data);
                $ret = $this->JSONformatter->createExpendedJson($fakeData);
            } else {
                $ret = $this->JSONformatter->createExpendedJson($data);
            }
        } else {
            $ret = $this->JSONformatter->createExpendedJson($data);
        }
        return $ret;
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
}
