<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;

class OptionTest extends BaseTestCase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
    }

    public function testDefaultLog()
    {
        $logger = new Logger();
        $logger->logger('defaultText');
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/defaultText(\s*)$/', $this->getContent());
    }

    public function testDefaulStatictLog()
    {
        \Debuggertools\Logger::loggerStatic('staticLogger');
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/staticLogger(\s*)$/', $this->getContent());
    }

    public function testHidePrefix()
    {
        $logger = new Logger(['hidePrefix' => 1]);
        $logger->logger('prefixHide');
        $this->assertMatchesRegularExpression('/^prefixHide(\s*)$/', $this->getContent());
        $this->assertMatchesRegularExpression('/^(?!.*\d{4}\/\d{2}\/\d{2}\.\d{2}:\d{2}:\d{2})/', $this->getContent()); // inexistance of previous log

    }

    public function testShowPrefix()
    {
        $logger = new Logger(['showPrefix' => 1]);
        $logger->logger('showPrefix');
        $this->assertMatchesRegularExpression('/\d{4}\/\d{2}\/\d{2}\.\d{2}:\d{2}:\d{2} : showPrefix(\s*)$/', $this->getContent());
    }

    public function testPurgeFile()
    {
        $logger = new Logger();
        $logger->logger('addTextBeforePurge');
        $this->assertMatchesRegularExpression('/addTextBeforePurge(\s*)$/', $this->getContent());
        //new logger
        $otherLogger = new Logger(['purgeFileBefore' => 1]);
        $otherLogger->logger('addTextAfterPurge');
        $this->assertMatchesRegularExpression('/addTextAfterPurge(\s*)$/', $this->getContent());
        $this->assertMatchesRegularExpression('/^(?!.*addTextBeforePurge)/', $this->getContent()); // inexistance of previous log
    }

    public function testLogOtherFile()
    {
        $logger = new Logger(['fileName' => 'foo']);
        $logger->logger('otherLog');
        $this->setPath('log/foo.log');
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/otherLog(\s*)$/', $this->getContent());
    }

    public function testLogOtherWithPathFile()
    {
        $logger = new Logger(['fileName' => 'foo/bar/example']);
        $logger->logger('log with path');
        $this->setPath('log/foo/bar/example.log');
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/log with path(\s*)$/', $this->getContent());
    }

    public function testDefaultArray()
    {
        $logger = new Logger();
        $logger->logger(['expend' => false]);
        $this->assertMatchesRegularExpression('/{"expend":false}(\s*)$/', $this->getContent());
    }

    public function testExpendArray()
    {
        $logger = new Logger(['expendObject' => 1]);
        $logger->logger(['expend' => true]);
        $this->assertMatchesRegularExpression('/{\n\s*expend : true\n}(\s*)/', $this->getContent());
    }
}
