<?php

namespace Test\Trace\Unit;

use Closure;
use Debuggertools\Trace;
use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\UserEntity;
use Test\ObjectForTest\TraceEntity;

class BaseTraceTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
    }

    public function testBaseTrace()
    {
        $Trace = new Trace();
        $Trace->logTrace();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/TRACE START/', $this->getContent());
        $this->assertMatchesRegularExpression('/TRACE END/', $this->getContent());
    }

    public function testStaticTrace()
    {
        Trace::getTraceStatic();
        $this->assertTrue($this->fileExist('log/log.log'));
        $this->assertMatchesRegularExpression('/TRACE START/', $this->getContent());
        $this->assertMatchesRegularExpression('/TRACE END/', $this->getContent());
    }

    public function testIngorePackageTrace()
    {
        Trace::getTraceStatic();

        $this->assertDoesNotMatchRegularExpression('/\/src\/Debuggertools\//', $this->getContent());
    }

    public function testFile()
    {
        Trace::getTraceStatic(['fileName' => 'trace']);
        $this->setPath('log/trace.log');
        $this->assertTrue($this->fileExist());
        $this->assertMatchesRegularExpression('/TRACE START/', $this->getContent());
        $this->assertMatchesRegularExpression('/TRACE END/', $this->getContent());
    }

    public function testDetectStatic()
    {
        self::staticValue();
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*:: staticValue\(\)/', $this->getContent());
    }

    public function testPrivateFunction()
    {
        self::privateFunction();
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> privateFunction\(\)/', $this->getContent());
    }

    public function testSeveralFunction()
    {
        self::firstFunction();
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> firstFunction\(\)/', $this->getContent());
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> secondFunction\(\)/', $this->getContent());
    }

    public function testClosureFunction()
    {
        $closure = function () {
            Trace::getTraceStatic();
        };
        $closure();
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> Test\\\\Trace\\\\Unit\\\\\{closure\}\(\)/', $this->getContent());
    }

    public function testtraceParamString()
    {
        self::traceParamString('string');
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> traceParamString\(string\)/', $this->getContent());
    }

    public function testtraceParamFloat()
    {
        self::traceParamFloat(0.1547474);
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> traceParamFloat\(0.1547474\)/', $this->getContent());
    }

    public function testtraceParamInt()
    {
        self::traceParamInt(15);
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> traceParamInt\(15\)/', $this->getContent());
    }

    public function testtraceParamObject()
    {
        self::traceParamObject(new UserEntity());
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> traceParamObject\(\'Test\\\\ObjectForTest\\\\UserEntity\'\)/', $this->getContent());
    }

    public function testtraceParamClosure()
    {
        self::traceParamClosure(function ($match) {
            return strtoupper($match[1]);
        });
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\Trace\\\\Unit\\\\BaseTraceTest\'\s*\n\s*-> traceParamClosure\(\'Closure\'\)/', $this->getContent());
    }

    public function testTraceInObjectPublic()
    {
        $TraceEntity = new TraceEntity();
        $TraceEntity->traceInPublic();
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> traceInPublic\(\)/', $this->getContent());
    }


    public function testTraceInObjectPrivate()
    {
        $TraceEntity = new TraceEntity();
        $TraceEntity->traceInPrivate();
        $this->assertMatchesRegularExpression('/tests\/ObjectForTest\/TraceEntity\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> functionPrivate\(\)/', $this->getContent());
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> traceInPrivate\(\)/', $this->getContent());
    }

    public function testTraceInObjectStatic()
    {
        $TraceEntity = new TraceEntity();
        $TraceEntity->traceInStatic();
        $this->assertMatchesRegularExpression('/tests\/ObjectForTest\/TraceEntity\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*:: functionStatic\(\)/', $this->getContent());
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> traceInStatic\(\)/', $this->getContent());
    }

    public function testTraceInObjectClosure()
    {
        $TraceEntity = new TraceEntity();
        $TraceEntity->traceInClosure();
        $this->assertMatchesRegularExpression('/tests\/ObjectForTest\/TraceEntity\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> Test\\\\ObjectForTest\\\\\{closure\}\(\)/', $this->getContent());
        $this->assertMatchesRegularExpression('/tests\/Trace\/Unit\/BaseTraceTest\.php \(line : \d+\)  \'Test\\\\ObjectForTest\\\\TraceEntity\'\s*\n\s*-> traceInClosure\(\)/', $this->getContent());
    }

    //function for test
    public static function staticValue()
    {
        Trace::getTraceStatic();
    }

    private function privateFunction()
    {
        Trace::getTraceStatic();
    }

    private function firstFunction()
    {
        self::secondFunction();
    }

    private function secondFunction()
    {
        Trace::getTraceStatic();
    }

    private function traceParamString(string $stringTest) //NOSONAR
    {
        Trace::getTraceStatic();
    }

    private function traceParamFloat(float $float) //NOSONAR
    {
        Trace::getTraceStatic();
    }

    private function traceParamInt(int $int) //NOSONAR
    {
        Trace::getTraceStatic();
    }

    private function traceParamObject(UserEntity $UserEntity) //NOSONAR
    {
        Trace::getTraceStatic();
    }

    private function traceParamClosure(Closure $closure) //NOSONAR
    {
        Trace::getTraceStatic();
    }
}
