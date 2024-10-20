<?php

declare(strict_types=1);

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
    protected  $rootPath = null;

    /**
     * Configurations
     *
     * @var Configurations|null
     */
    private $Configurations = null;

    public function __construct()
    {
        $this->regexVendorFolder = '\\' . (DIRECTORY_SEPARATOR) . 'vendor' . '\\' . (DIRECTORY_SEPARATOR);
        $this->regexDebuggertools = '\\' . (DIRECTORY_SEPARATOR) . 'Debuggertools' . '\\' . (DIRECTORY_SEPARATOR) . 'Config';

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
                    $postPath = $pathConfig;
                } else {
                    return $this->rootPath = $pathConfig . DIRECTORY_SEPARATOR;
                }
            }
        }

        //check if in vendor folder
        if (preg_match('/' . $this->regexVendorFolder . '/', $path)) {
            return $this->rootPath = preg_replace('/' . $this->regexVendorFolder . '.*$/', '',  $path) . $postPath;
        }

        //local test for dev with docker
        if (preg_match('/' . $this->regexDebuggertools . '/', $path)) {
            $regex = '/' . $this->regexDebuggertools . '.*$/';

            $this->rootPath = preg_replace($regex, DIRECTORY_SEPARATOR . '..',  $path);
            if ($postPath) $this->rootPath .= $postPath;
            return $this->rootPath;
        }

        return $this->rootPath = dirname(__DIR__) . $postPath;
    }

    public function getLogFolderPath()
    {
        return $this->getRootPath() . DIRECTORY_SEPARATOR . 'log';
    }
}
