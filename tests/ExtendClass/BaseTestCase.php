<?php

namespace Test\ExtendClass;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    private $projectFolder = "";
    protected $defaulPathLog =  'log' . DIRECTORY_SEPARATOR . 'log.log';

    public function __construct()
    {
        parent::__construct();

        $BasePath = __DIR__;
        $this->projectFolder = preg_replace('/\/tests(\/.*)$/', DIRECTORY_SEPARATOR, $BasePath);
        $this->projectFolder .= 'Dev'; //add base folder
    }

    protected function fileExist(string $basePath = null): bool
    {
        if (is_null($basePath)) {
            $basePath = $this->defaulPathLog;
        }
        return file_exists($this->projectFolder . DIRECTORY_SEPARATOR . $basePath);
    }

    protected function getContent(string $basePath = null): string
    {
        if (is_null($basePath)) {
            $basePath = $this->defaulPathLog;
        }
        if ($this->fileExist($basePath)) {
            return file_get_contents($this->projectFolder . DIRECTORY_SEPARATOR . $basePath);
        }
        return '';
    }


    protected function purgeLog(string $path = null): void
    {

        if (!$path) {
            $path = $this->projectFolder;
        }

        if ($path) {
            $this->deleteDirectory($path);
        }
    }


    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return false;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }
}
