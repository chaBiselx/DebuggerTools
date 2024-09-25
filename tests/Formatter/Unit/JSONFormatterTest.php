<?php

namespace Test\Formatter\Unit;

use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\RoleEntity;
use Debuggertools\Config\InstanceConfig;
use Debuggertools\Formatter\JSONformatter;
use Debuggertools\Enumerations\OptionForInstanceEnum;

class JSONFormatterTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->instanceConfig = new InstanceConfig();
        $this->JSONformatter = new JSONformatter();
    }

    public function testArraySimpleNotExpend()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson([10, 'string', 1245, 12.65, true, false, new RoleEntity]);
        $this->assertEquals('[10,"string",1245,12.65,true,false,{"permissions":[]}]', $result);
    }

    public function testArraySimpleExpendStringKey()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson([10, 'string', 1245, 12.65, true, false, new RoleEntity]);
        $this->assertMatchesRegularExpression('/\[\n {2,}0 : 10,\n {2,}1 : "string"\,\n {2,}2 : 1245,\n {2,}3 : 12.65,\n {2,}4 : true,\n {2,}5 : false,\n {2,}6 : \n {2,}\{\n {4,}"permissions" : \[\],\n {2,}\}\n\]/', $result);
    }

    public function testTextWithQuoteInArrayNotExpend()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson(['Hello "John Doe" ']);
        $this->assertEquals('["Hello \\"John Doe\\" "]', $result);
    }

    public function testTextWithQuoteInArrayExpened()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson(['Hello "John Doe" ']);
        $this->assertMatchesRegularExpression('/\[\n {2,}0 : "Hello \\\\"John Doe\\\\" "\n\]/', $result);
    }

    public function testTextWithQuoteInAssociativeArray()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson(['key' => 'Hello "John Doe" ']);
        $this->assertMatchesRegularExpression('/\{\n {2,}"key" : "Hello \\\\"John Doe\\\\" "\n\}/', $result);
    }

    public function testAssociativeArrayNotExpend()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson(['el' => true]);
        $this->assertEquals('{"el":true}', $result);
    }

    public function testAssociativeArrayExpendStringKey()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson(['el' => true]);
        $this->assertMatchesRegularExpression('/\{\n {2,}"el" : true\n\}/', $result);
    }

    public function testMultiLevelArrayNotExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson([['el1'], [['el2']]]);
        $this->assertEquals('[["el1"],[["el2"]]]', $result);
    }

    public function testMultiLevelArrayExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson([['el1'], [['el2']]]);
        $this->assertMatchesRegularExpression('/\[\n {2,}0 : \n {2,}\[\n {4,}0 : "el1"\n {2,}\],\n {2,}1 : \n {2,}\[\n {4,}0 : \n {4,}\[\n {6,}0 : "el2"\n {4,}\]\n {2,}\]\n\]/', $result);
    }

    public function testMultiLevelAssociativeArrayNotExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson(["levelA1" => ["levelA1A" => 'el1'], "levelA2" => ["levelA2A" => ["levelA2A1" => 'el2']]]);
        $this->assertEquals('{"levelA1":{"levelA1A":"el1"},"levelA2":{"levelA2A":{"levelA2A1":"el2"}}}', $result);
    }

    public function testMultiLevelAssociativeArrayExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson(["levelA1" => ["levelA1A" => 'el1'], "levelA2" => ["levelA2A" => ["levelA2A1" => 'el2']]]);
        $this->assertMatchesRegularExpression('/\{\n {2,}"levelA1" : \n {2,}\{\n {4,}"levelA1A" : "el1"\n {2,}\},\n {2,}"levelA2" : \n {2,}\{\n {4,}"levelA2A" : \n {4,}\{\n {6,}"levelA2A1" : "el2"\n {4,}\}\n {2,}\}\n\}/', $result);
    }

    public function testBoolTrueInKeyNotExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson([true => 1]);
        $this->assertEquals('{"1":1}', $result);
    }

    public function testBoolTrueInKeyExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson([true => 1]);
        $this->assertMatchesRegularExpression('/\{\n {2,}1 : 1\n\}/', $result);
    }

    public function testBoolFalseInKeyNotExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, false);
        $result = $this->JSONformatter->createExpendedJson([false => 1]);
        $this->assertEquals('[1]', $result);
    }

    public function testBoolFalseInKeyExpended()
    {
        $this->instanceConfig->set(OptionForInstanceEnum::EXPEND_OBJECT, true);
        $result = $this->JSONformatter->createExpendedJson([false => 1]);
        $this->assertMatchesRegularExpression('/\[\n {2,}0 : 1\n\]/', $result);
    }
}
