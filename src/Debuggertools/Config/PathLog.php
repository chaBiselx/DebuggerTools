<?php

namespace Debuggertools\Config;

use Debuggertools\Config\Configurations;

class PathLog
{
	private string $regexVendorFolder = '\\\\vendor\\\\';
	private string $regexDebuggertools = '\\\\Debuggertools\\\\';
	protected ?string $rootPath = NULL;
	private ?Configurations $Configurations = NULL;

	public function __construct()
	{
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
				if (preg_match('/^\\\\/', $pathConfig)) {
					$postPath =  $pathConfig . '\\';
				} else {
					return $this->rootPath = $pathConfig . '\\';
				}
			}
		}

		//check if in vendor folder
		if (preg_match('/' . $this->regexVendorFolder . '/', $path)) {
			return $this->rootPath = preg_replace('/' . $this->regexVendorFolder . '.*$/', '',  $path) . $postPath;
		}

		//
		if (preg_match('/' . $this->regexDebuggertools . '/', $path)) {
			return $this->rootPath = preg_replace('/' . $this->regexDebuggertools . '.*$/', '\\..',  $path) . $postPath;
		}

		return $this->rootPath = dirname(__DIR__) . $postPath;
	}

	public function getLogFolderPath()
	{
		return $this->getRootPath() . '\\log';
	}
}
