<?php

namespace DebuggerTools;

class CustomLog
{

	//PSR -> class abstrait 

	/**
	 * logger
	 *
	 * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data
	 * @param array $Option
	 * 
	 * list param for Options
	 * @var bool hideDate Hide the date at the beginning of the string
	 * @var bool purgeFileBefore purge the file before write
	 * @var bool expendObject expend for best visibility in log file
	 * @var string fileName write in the file with the same name default: log
	 * 
	 * @return void
	 */
	public static function logger($data, $Option = [])
	{
		$texts = [];
		$path = dirname(__DIR__) . "/dev/log";

		self::createDirIfNotExist($path);

		$fileName = "log";
		if (isset($Option['fileName']) && $Option['fileName']) {
			$fileName = $Option['fileName'];
		}
		$pathFile = $path . "/$fileName.log";
		if (isset($Option['purgeFileBefore']) && $Option['purgeFileBefore']) { //reset file
			file_put_contents($pathFile, '');
		}

		$expendObject = false;
		if (isset($Option['expendObject']) && $Option['expendObject']) { // expend object / array
			$expendObject = true;
		}

		//check type and get contennt
		$type = gettype($data);
		switch ($type) {
			case 'object':
				if (is_object($data)) { // class 
					$dataDecode = self::decodeObjet($data);
					$texts[0] = $type . " '" . $dataDecode['class'] . "' : " . self::createExpendedJson($dataDecode['content'], $expendObject);
					if (isset($dataDecode['appendLog']) && $dataDecode['appendLog']) {
						$texts = array_merge($texts, $dataDecode['appendLog']);
					}
				} else { // simple object 
					$texts[0] = $type . " : " . self::createExpendedJson($data, $expendObject);
				}
				break;
			case 'array':
				if (count($data) > 0) {
					if (isset($data[0]) && gettype($data[0]) == "object") {
						$fakeData = self::decodeListObjet($data);
						$texts[0] = $type . " : " . self::createExpendedJson($fakeData, $expendObject);
					} else {
						$texts[0] = $type . " : " . self::createExpendedJson($data, $expendObject);
					}
				} else {
					$texts[0] = $type . " : " . self::createExpendedJson($data, $expendObject);
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
		$prefixText = date('Y/m/d.H:i:s') . ' : ';
		foreach ($texts as $text) {
			if (!isset($Option['hideDate']) || !$Option['hideDate']) { // hide prefix
				$text = $prefixText . $text;
			}
			file_put_contents($pathFile, $text . "\n", FILE_APPEND);
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
	public static function createExpendedJson($data, bool $expendObject = false, $nbSpace = 0): string
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
					self::has_string_keys($data)
				) {
					$srtCroche = '{';
					$endCroche = '}';
				} else {
					$srtCroche = '[';
					$endCroche = ']';
				}

				$stringResponse .= $indent . $srtCroche . "\n";
				foreach ($data as $key => $subData) {
					$stringResponse .= $indent . "  " . $key . " : " . self::createExpendedJson($subData, $expendObject, $nbSpace + 2) . "\n";
				}
				$stringResponse .= $indent . $endCroche;
			} else {
				$stringResponse = $indent . json_encode($data);
			}
		} else {
			if ($data === NULL) $data = "NULL";
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
	public static function has_string_keys(array $array): bool
	{
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

	/**
	 * Create a dir if not exist
	 *
	 * @param string $path
	 * @return void
	 */
	public static function createDirIfNotExist(string $path): void
	{
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
	}

	/**
	 * Analyse the object to give the classname, the content and if necesary log to append
	 *
	 * @param mixed $obj
	 * @return array
	 */
	public static function decodeObjet($obj): array
	{
		$dataToReturn = [
			'class' => NULL,
			'content' => NULL,
			'appendLog' => []
		];

		$class = get_class($obj); // get classname
		$fakeData = json_decode(json_encode($obj), true); // clone the public data 
		$appendLog = [];
		// get private var with getter
		foreach (get_class_methods($obj) as $key => $function) {

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
		$returnAppendLog = self::getContentSpecialClass($obj);
		if ($returnAppendLog) {
			$appendLog = arra_merge($appendLog, $returnAppendLog);
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
	private static function getContentSpecialClass($obj): array
	{
		$toAppendToLog = [];
		if (class_exists('Doctrine\\ORM\\QueryBuilder')) {
			if ($obj instanceof \Doctrine\ORM\QueryBuilder) {
				$appendLog[] = $obj->getQuery()->getSql();
			}
		}
		return $toAppendToLog;
	}

	/**
	 * Decode a liste of objects
	 *
	 * @param mixed $arrayofObject
	 * @return array
	 */
	public static function decodeListObjet($arrayofObject): array
	{
		$fakeData = [];

		foreach ($arrayofObject as $object) {
			$objectDecode = self::decodeObjet($object);
			$fakeData[] = ['class' => $objectDecode['class'], 'content' => $objectDecode['content']];
		}

		return $fakeData;
	}
}
