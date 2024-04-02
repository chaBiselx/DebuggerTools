<?php

namespace Debuggertools\Config;

use Debuggertools\Config\Configurations;

class PathLog
{
	/**
	 * regexVendorFolder
	 *
	 * @var string
	 */
	private $regexVendorFolder = "";

	/**
	 * regexDebuggertools
	 *
	 * @var string
	 */
	private $regexDebuggertools = "";

	/**
	 * rootPath
	 *
	 * @var string|null
	 */
	protected  $rootPath = NULL;

	/**
	 * Configurations
	 *
	 * @var Configurations|null
	 */
	private $Configurations = NULL;

	public function __construct()
	{
		$this->regexVendorFolder = '\\' . (DIRECTORY_SEPARATOR) . 'vendor' . '\\' . (DIRECTORY_SEPARATOR);
		$this->regexDebuggertools = '\\' . (DIRECTORY_SEPARATOR) . 'Debuggertools' . '\\' . (DIRECTORY_SEPARATOR);

		$this->Configurations = new Configurations();
	}


	private function getRootPath()
	{
		$path = __DIR__;
		$postPath = "";

		//use json config 
		$Config = $this->Configurations->getConfig();
		if ($Config && $Config['fileLog']) {
			$pathConfig = $Config['fileLog']['folder']['path'] ?? null;
			if ($pathConfig) {
				if (preg_match('/^\\' . DIRECTORY_SEPARATOR . '/', $pathConfig)) {
					$postPath =  DIRECTORY_SEPARATOR . $pathConfig . DIRECTORY_SEPARATOR;
				} else {
					return $this->rootPath = $pathConfig . DIRECTORY_SEPARATOR;
				}
			}
		}

		//check if in vendor folder
		if (preg_match('/' . $this->regexVendorFolder . '/', $path)) {
			return $this->rootPath = preg_replace('/' . $this->regexVendorFolder . '.*$/', '',  $path) . $postPath;
		}

		//local test
		if (preg_match('/' . $this->regexDebuggertools . '/', $path)) {
			return $this->rootPath = preg_replace('/' . $this->regexDebuggertools . '.*$/', DIRECTORY_SEPARATOR . '..',  $path) . $postPath;
		}

		return $this->rootPath = dirname(__DIR__) . $postPath;
	}

	public function getLogFolderPath()
	{
		return realpath($this->getRootPath() . DIRECTORY_SEPARATOR . 'log');
	}
}
