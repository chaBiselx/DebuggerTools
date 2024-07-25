<?php

namespace Debuggertools\ExtendClass;

use Debuggertools\Config\PathLog;
use Debuggertools\Traits\FileSystem;
use Debuggertools\Config\Configurations;
use Debuggertools\Objects\ObjectDecoder;
use Debuggertools\Objects\SymfonyQueryBuilder;

abstract class AbstractCustomLog
{
    use FileSystem;

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
        $path = (new PathLog())->getLogFolderPath();
        $this->setDefaultWithConfig();
        $this->createDirIfNotExist($path); // FileSystem
        $this->pathFile = $path . DIRECTORY_SEPARATOR . $this->fileName . "." . $this->fileExtension;

        $this->ObjectDecoder = new ObjectDecoder();
        $this->SymfonyQueryBuilder = new SymfonyQueryBuilder();
    }

    private function setDefaultWithConfig()
    {
        $this->config = (new Configurations)->getConfig();
        //file
        $this->fileName = $this->config['fileLog']['defaultName'] ?? 'log';
        $this->fileExtension = $this->config['fileLog']['defaultExtension'] ?? 'log';
        //bool
        $this->showPrefix = $this->config['prefixLog']['defaultShowPrefix'] ?? true;
    }

    /**
     * Generate an array of text for the logger
     *
     * @param mixed $data
     * @return array
     */
    protected function generateTextFormData(mixed $data): array
    {
        //check type and get contennt
        $type = gettype($data);
        switch ($type) {
            case 'object':
                if (is_object($data)) { // class or complexe object
                    $dataDecode = $this->decodeObjet($data);
                    $text = $type . " '" . $dataDecode['class'] . "' : "; //write type
                    $text .= $this->createExpendedJson($dataDecode['content'], $this->expendObject); //Write content of the object
                    $texts[0] = $text;
                    if (isset($dataDecode['appendLog']) && $dataDecode['appendLog']) {
                        $texts = array_merge($texts, $dataDecode['appendLog']); // write more informations
                    }
                } else { // simple object
                    $texts[0] = $type . " : " . $this->createExpendedJson($data, $this->expendObject);
                }
                break;
            case 'array':
                $texts[0] = $type . " : " . $this->decodeArrayForLog($data);
                break;
            case 'integer':
            case 'float':
            case 'double':
            case 'string':
                $texts[0] = $data;
                break;
            case 'boolean':
                $texts[0] = $type . ' : ' . ($data ? 'TRUE' : 'FALSE');
                break;
            default:
                $texts[0] = $type;
                break;
        }
        return $texts;
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
    protected  function createExpendedJson($data, bool $expendObject = false, $nbSpace = 0): string
    {
        $stringResponse = '';
        $type = gettype($data);
        //base indent
        $indent = self::createIndent($nbSpace);

        if (in_array($type, ['object', 'array'])) {
            if ($expendObject) {
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
                    $stringResponse .= $this->createExpendedJson($subData, $expendObject, $nbSpace + 2) . "\n";
                }
                $stringResponse .= $indent . $endCroche;
            } else {
                $stringResponse = $indent . json_encode($data);
            }
        } else {
            if ($data === null) $data = "null";
            $stringResponse = (string) $data;
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
     * Analyse the object to give the classname, the content and if necesary log to append
     *
     * @param mixed $obj
     * @return array
     */
    protected  function decodeObjet($obj): array
    {
        $dataToReturn = [
            'class' => null,
            'content' => null,
            'appendLog' => []
        ];

        if (gettype($obj) == 'object') {
            $class = get_class($obj); // get classname
            $appendLog = [];
            $fakeData = $this->ObjectDecoder->decodeObject($obj);

            //check instance for more data
            $returnAppendLog = $this->getContentSpecialClass($obj);
            if ($returnAppendLog) {
                $appendLog = array_merge($appendLog, $returnAppendLog);
            }
        } else {
            $class = gettype($obj);
            $fakeData = $obj;
        }


        if (isset($class)) $dataToReturn['class'] = $class;
        if (isset($fakeData)) $dataToReturn['content'] = $fakeData;
        if (isset($appendLog)) $dataToReturn['appendLog'] = $appendLog;


        return $dataToReturn;
    }

    /**
     * Get more content from spécial class
     *
     * @param object $obj
     * @return array
     */
    protected  function getContentSpecialClass($obj): array
    {
        $toAppendToLog = [];
        if (
            class_exists('Doctrine\\ORM\\QueryBuilder') &&
            $obj instanceof \Doctrine\ORM\QueryBuilder
        ) {
            $toAppendToLog = array_merge($toAppendToLog, $this->SymfonyQueryBuilder->returnForLog($obj));
        }
        return $toAppendToLog;
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
            $objectDecode = $this->decodeObjet($object);
            $fakeData[] = ['class' => $objectDecode['class'], 'content' => $objectDecode['content']];
        }

        return $fakeData;
    }

    protected function decodeArrayForLog($data): string
    {
        $ret = "";
        if (!empty($data)) {
            if (isset($data[0]) && gettype($data[0]) == "object") {
                $fakeData = $this->decodeListObjet($data);
                $ret = $this->createExpendedJson($fakeData, $this->expendObject);
            } else {
                $ret = $this->createExpendedJson($data, $this->expendObject);
            }
        } else {
            $ret = $this->createExpendedJson($data, $this->expendObject);
        }
        return $ret;
    }
}
