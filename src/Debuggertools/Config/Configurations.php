<?php

namespace Debuggertools\Config;

use Debuggertools\Traits\FileSystem;

class Configurations
{
	use FileSystem;
	private string $pathRelativeJson = '\\json';
	private string $nameJson = 'config.local.json';
	private string $pathFile = 'config.local.json';
	private array $defaultJson =
	[
		"fileLog" => [
			"folder" => [
				"path" => null,
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
		$this->pathFile = $Path . '\\' . $this->nameJson;
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
		file_put_contents($this->pathFile, $text);
	}
}
