<?php

namespace Debuggertools;

use Debuggertools\ExtendClass\AbstractCustomLog;

class CustomLog extends AbstractCustomLog
{
	/**
	 * Stored data for time measurement
	 *
	 * @var array
	 */
	// private array $timeLogger = []; >= php7.4
	private $timeLogger = [];

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
		$texts = [];

		//check type and get contennt
		$type = gettype($data);
		switch ($type) {
			case 'object':
				if (is_object($data)) { // class 
					$dataDecode = $this->decodeObjet($data);
					$texts[0] = $type . " '" . $dataDecode['class'] . "' : " . $this->createExpendedJson($dataDecode['content'], $this->expendObject);
					if (isset($dataDecode['appendLog']) && $dataDecode['appendLog']) {
						$texts = array_merge($texts, $dataDecode['appendLog']);
					}
				} else { // simple object 
					$texts[0] = $type . " : " . $this->createExpendedJson($data, $this->expendObject);
				}
				break;
			case 'array':
				if (count($data) > 0) {
					if (isset($data[0]) && gettype($data[0]) == "object") {
						$fakeData = $this->decodeListObjet($data);
						$texts[0] = $type . " : " . $this->createExpendedJson($fakeData, $this->expendObject);
					} else {
						$texts[0] = $type . " : " . $this->createExpendedJson($data, $this->expendObject);
					}
				} else {
					$texts[0] = $type . " : " . $this->createExpendedJson($data, $this->expendObject);
				}
				break;
			case 'integer':
			case 'float':
			case 'double':
			case 'string':
				$texts[0] = $data;
				break;
			case 'boolean':
				$texts[0] = $type . ' : ' . ($data == true ? 'TRUE' : 'FALSE');
				break;
			default:
				$texts[0] = $type;
				break;
		}

		// write log
		$this->writeInLog($texts);
	}

	/**
	 * static logger
	 *
	 * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data , same data as logger
	 * @param  $Option , same data as constructor
	 * 
	 * @return void
	 */
	public static function SLogger($data, array $Option = []): void
	{
		(new CustomLog($Option))->logger($data);
	}

	public function time($label = 'time')
	{
		if (!isset($this->timeLogger[$label])) {
			$this->timeLogger[$label] = microtime(true);
		} else {
			$time = round(microtime(true) - $this->timeLogger[$label], 4);
			$this->logger("$label => $time Sec");
		}
	}
}
