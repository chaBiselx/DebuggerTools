<?php

namespace Debuggertools;

use Debuggertools\Abstract\AbstractCustomLog;

class CustomLog extends AbstractCustomLog
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
			$this->fileName = $Option['fileName'];
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
}
