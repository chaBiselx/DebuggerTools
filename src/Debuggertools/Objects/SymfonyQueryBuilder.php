<?php

namespace Debuggertools\Objects;



class SymfonyQueryBuilder
{
	public static function returnForLog(\Doctrine\ORM\QueryBuilder $obj): array
	{
		$retLog = [];
		$sql = $obj->getQuery()->getSql();
		$listKeys = $obj->getQuery()->getParameters()->getKeys();
		$listValues = $obj->getQuery()->getParameters()->getValues();
		$listParam = [];
		foreach ($listKeys as $key) {
			$parameter = $listValues[$key];
			$listParam[$key] = self::decodeListObjetSpecialClassQueryBuilder($parameter);
		}
		$retLog[] = $sql;
		$retLog[] = json_encode($listParam);

		return $retLog;
	}

	private static function decodeListObjetSpecialClassQueryBuilder($parameter): string
	{
		$value = 'TO_DEFINE';
		switch ($parameter->getType()) {
			case 'datetime':
				$value = "'" . $parameter->getValue()->format('Y-m-d H:i:s') . "'";
				break;
			case '2': // string
			case 'string':
				$value = "'" . $parameter->getValue() . "'";
				break;
			case 'integer': // string
				$value =  $parameter->getValue();
				break;
			case 102:
				$value = "";
				foreach ($parameter->getValue() as $k => $v) {
					if ($k > 0) $value .= ", ";
					$value .= "'" . $v . "'";
				}
				break;
			default:
				$d = self::decodeObjet($parameter);
				$value .= " : " . $d['class'] . " => " . json_encode($d['content']);
				break;
		}
		return $value;
	}

	/**
	 * Analyse the object to give the classname, the content and if necesary log to append
	 *
	 * @param mixed $obj
	 * @return string
	 */
	protected  function decodeObjetParameter($parameter): string
	{
		$class = get_class($parameter); // get classname
		$decoded =  ' "' . $class . '" ' . $parameter->getType() . ' : ' . json_decode(json_encode($parameter->getValue()), true);

		return $decoded;
	}
}
