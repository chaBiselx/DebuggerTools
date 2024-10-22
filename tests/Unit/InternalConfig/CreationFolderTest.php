<?php

declare(strict_types=1);

namespace Test\Unit\InternalConfig;

use Debuggertools\Logger;
use Test\ExtendClass\BaseTestCase;

class CreationFolderTest extends BaseTestCase
{

    private $folder;

    public function setup(): void
    {
        parent::setUp();
        $this->folder = $this->getProjectFolder();
        if (is_dir($this->folder)) {
            $this->deleteDirectory($this->folder);
        }
        $this->logger = new Logger();
    }

    public function testCreationWrite777(): void
    {
        $permissions = '0777';
        $this->creationFolder($permissions);
        $this->assertTrue(is_dir($this->folder));
        $this->assertTrue(is_writable($this->folder));
        $this->logger->logger('test');
        $this->assertTrue($this->fileExist('log/log.log'));
    }

    public function testCreationWrite755(): void
    {
        $permissions = '0755';
        $this->creationFolder($permissions);
        $this->assertTrue(is_dir($this->folder));
        $this->assertTrue(is_writable($this->folder));
        $this->logger->logger('test');
        $this->assertTrue($this->fileExist('log/log.log'));
    }

    public function testCreationWrite666(): void
    {
        $permissions = '0666';
        $this->creationFolder($permissions);
        $this->assertTrue(is_dir($this->folder));
        $this->assertTrue(is_writable($this->folder));
        $this->logger->logger('test');
        $this->assertTrue($this->fileExist('log/log.log'));
    }

    // public function testCreationWrite000(): void
    // {
    //     $permissions = '0000';
    //     $this->creationFolder($permissions);
    //     $this->assertTrue(is_dir($this->folder));
    //     $this->assertTrue(!is_writable($this->folder));
    // }

    private function creationFolder(string $permission): void
    {
        system("mkdir -p " . $this->folder);
        system("chmod -R " . $permission . " " . $this->folder);
    }

    private function deleteDirectory($dirPath) {
        if (is_dir($dirPath)) {
           $files = scandir($dirPath);
           foreach ($files as $file) {
              if ($file !== '.' && $file !== '..') {
                 $filePath = $dirPath . '/' . $file;
                 if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                 } else {
                    unlink($filePath);
                 }
              }
           }
           rmdir($dirPath);
        }
     }
}
