<?php

namespace Debuggertools\Config;

use Debuggertools\Traits\FileSystem;

class Configurations
{
	use FileSystem;
	/**
	 * pathRelativeJson folder
	 *
	 * @var string
	 */
	private $pathRelativeJson = DIRECTORY_SEPARATOR . 'json';

	/**
	 * nameJson name of the file
	 *
	 * @var string
	 */
	private $nameJson = 'config.local.json';

	/**
	 * Path file 
	 *
	 * @var string
	 */
	private $pathFile = '';
	/**
	 * default Json
	 *
	 * @var array
	 */
	private $defaultJson =
	[
		"fileLog" => [
			"folder" => [
				"path" => 'dev' . DIRECTORY_SEPARATOR,
			],
			"defaultName" => "log",
			"defaultExtension" => "log",
		],
		"prefixLog" => [
			"defaultShowPrefix" => true,
			"date" => [
				"format" => "Y/m/d.H:i:s",
				"separator" => " : "
			]
		]
	];

	public function __construct()
	{
		$Path = __DIR__ . $this->pathRelativeJson;
		$this->createDirIfNotExist($Path); // FileSystem
		$this->pathFile = $Path . DIRECTORY_SEPARATOR . $this->nameJson;
	}


	public function getConfig()
	{
		return file_exists($this->pathFile) ? $this->getContentFile() : $this->createDefaultFile();
	}

	/**
	 * return the content of config
	 *
	 * @return array
	 */
	private function getContentFile(): array
	{
		return json_decode(file_get_contents($this->pathFile), true) + $this->defaultJson;
	}

	/**
	 * return the default config and create de contig file
	 *
	 * @return array
	 */
	private function createDefaultFile(): array
	{
		$textJson = json_encode($this->defaultJson);
		$this->writeFile($textJson);
		return $this->defaultJson;
	}

	/**
	 * actualize file config with default values
	 *
	 * @return void
	 */
	private function actualizeConfig(): void
	{
		$this->writeFile(json_encode(json_decode(file_get_contents($this->pathFile), true) + $this->defaultJson));
	}

	/**
	 * write the config file 
	 *
	 * @param string $text
	 * @return void
	 */
	private function writeFile(string $text)
	{
		if (!file_exists($this->pathFile)) touch($this->pathFile);
		file_put_contents($this->pathFile, $text);
	}
}
