<?php

declare(strict_types=1);

namespace Test\Fonctionnal\Logger;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\Entity\UserEntity;

class ClosureTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testClosure()
    {
        $function = function () {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \[\]$/', $this->getContent());
    }

    public function testParamSimple()
    {
        $function = function ($Param1) {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"parameters":\[\{"name":"Param1"\}\]\}\n$/', $this->getContent());
    }

    public function testParamDouble()
    {
        $function = function ($Param1, $Param2) {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"parameters":\[\{"name":"Param1"\},\{"name":"Param2"\}\]\}\n$/', $this->getContent());
    }

    public function testTypeParamSimple()
    {
        $function = function (int $Param1) {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"parameters":\[\{"name":"Param1","type":"int"\}\]\}\n$/', $this->getContent());
    }

    public function testTypeParamDouble()
    {
        $function = function (array $Param1, float $Param2) {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"parameters":\[\{"name":"Param1","type":"array"\},\{"name":"Param2","type":"float"\}\]\}\n$/', $this->getContent());
    }

    public function testTypeParamClass()
    {
        $function = function (UserEntity $Param1) {
            return 'testOK';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"parameters":\[\{"name":"Param1","type":"Test\\\\ObjectForTest\\\\Entity\\\\UserEntity"\}\]\}\n$/', $this->getContent());
    }

    public function testReturnType()
    {
        $function = function (): string {
            return 'string';
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"returnType":"string"\}$/', $this->getContent());
    }

    public function testReturnTypeClass()
    {
        $function = function (): UserEntity {
            return new UserEntity();
        };
        $this->Logger->logger($function);
        $this->assertMatchesRegularExpression('/ : class \'Closure\' : \{"returnType":"Test\\\\ObjectForTest\\\\Entity\\\\UserEntity"\}$/', $this->getContent());
    }
}
