<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;
use Debuggertools\Enumerations\OptionForInstanceEnum;

class OptionTest extends BaseTestCase
{

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
        $logger = new Logger([OptionForInstanceEnum::PREFIX_HIDE => 1]);
        $logger->logger('prefixHide');
        $this->assertMatchesRegularExpression('/^prefixHide(\s*)$/', $this->getContent());
        $this->assertMatchesRegularExpression('/^(?!.*\d{4}\/\d{2}\/\d{2}\.\d{2}:\d{2}:\d{2})/', $this->getContent()); // inexistance of previous log

    }

    public function testShowPrefix()
    {
        $logger = new Logger([OptionForInstanceEnum::PREFIX_SHOW => 1]);
        $logger->logger('showPrefix');
        $this->assertMatchesRegularExpression('/\d{4}\/\d{2}\/\d{2}\.\d{2}:\d{2}:\d{2} : showPrefix(\s*)$/', $this->getContent());
    }

    public function testPurgeFile()
    {
        $logger = new Logger();
        $logger->logger('addTextBeforePurge');
        $this->assertMatchesRegularExpression('/addTextBeforePurge(\s*)$/', $this->getContent());
        //new logger
        $otherLogger = new Logger([OptionForInstanceEnum::ACTIVE_PURGE_FILE => 1]);
        $otherLogger->logger('addTextAfterPurge');
        $this->assertMatchesRegularExpression('/addTextAfterPurge(\s*)$/', $this->getContent());
        $this->assertMatchesRegularExpression('/^(?!.*addTextBeforePurge)/', $this->getContent()); // inexistance of previous log
    }

    public function testLogOtherFile()
    {
        $logger = new Logger([OptionForInstanceEnum::FILE_NAME => 'foo']);
        $logger->logger('otherLog');
        $this->setPath('log/foo.log');
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/otherLog(\s*)$/', $this->getContent());
    }

    public function testLogOtherWithPathFile()
    {
        $logger = new Logger([OptionForInstanceEnum::FILE_NAME => 'foo/bar/example']);
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
        $logger = new Logger([OptionForInstanceEnum::EXPEND_OBJECT => 1]);
        $logger->logger(['expend' => true]);
        $this->assertMatchesRegularExpression('/{\n\s*"expend" : true\n}(\s*)/', $this->getContent());
    }
}
