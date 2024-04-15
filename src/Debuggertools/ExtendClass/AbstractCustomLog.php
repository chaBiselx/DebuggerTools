<?php

namespace Debuggertools\ExtendClass;

use Debuggertools\Config\PathLog;
use Debuggertools\Traits\FileSystem;
use Debuggertools\Config\Configurations;
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

    //write in file
    protected  function writeInLog(array $texts)
    {
        if (!$this->pathFile) throw new \Error("Unknown file");
        if (!file_exists($this->pathFile)) touch($this->pathFile);
        $dateFormat = $this->config['prefixLog']['date']['format'];
        $separator = $this->config['prefixLog']['date']['separator'];
        $prefixText = date($dateFormat) . $separator;
        foreach ($texts as $text) {
            if ($this->showPrefix) {
                $text = $prefixText . $text;
            }
            file_put_contents($this->pathFile, $text . "\n", FILE_APPEND);
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
        $indent = "";
        for ($i = 0; $i < $nbSpace; $i++) {
            $indent .= " ";
        }

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
            $fakeData = json_decode(json_encode($obj), true); // clone the public data
            $appendLog = [];
            // get private var with getter
            foreach (get_class_methods($obj) as $function) {

                if (preg_match('/^get/', $function)) {
                    $method = new \ReflectionMethod($class, $function);
                    try {
                        if (empty($method->getParameters())) { // not parameters
                            $res =  $obj->$function();
                            if (gettype($res) != 'object') {
                                $fakeData["->$function"] = $obj->$function();
                            } else {
                                $fakeData["->$function"] = [get_class($res) => $obj];
                            }
                        }
                    } catch (\Error $e) {
                        $fakeData["->$function"] = ["CUSTOMLOG" => "ERROR LOGGER", "MESSAGE" => $e->getMessage()];
                    }
                }
            }

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
            $toAppendToLog = array_merge($toAppendToLog, SymfonyQueryBuilder::returnForLog($obj));
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
