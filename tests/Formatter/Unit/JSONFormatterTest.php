<?php

namespace Test\Formatter\Unit;

use Test\ExtendClass\BaseTestCase;
use Debuggertools\Config\InstanceConfig;
use Debuggertools\Formatter\JSONformatter;
use Test\ObjectForTest\RoleEntity;

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
        $this->instanceConfig->set('expendObject', false);
        $result = $this->JSONformatter->createExpendedJson([10, 'string', 1245, 12.65, true, false, new RoleEntity]);
        $this->assertEquals('[10,"string",1245,12.65,true,false,{"permissions":[]}]', $result);
    }

    public function testArraySimpleExpendStringKey()
    {
        $this->instanceConfig->set('expendObject', true);
        $result = $this->JSONformatter->createExpendedJson([10, 'string', 1245, 12.65, true, false, new RoleEntity]);
        $this->assertMatchesRegularExpression('/\[\n {2,}0 : 10,\n {2,}1 : string\,\n {2,}2 : 1245,\n {2,}3 : 12.65,\n {2,}4 : true,\n {2,}5 : false,\n {2,}6 : \n {2,}\{\n {4,}"permissions" : \[\],\n {2,}\}\n\]/', $result);
    }


    public function testAssociativeArrayNotExpend()
    {
        $this->instanceConfig->set('expendObject', false);
        $result = $this->JSONformatter->createExpendedJson(['el' => true]);
        $this->assertEquals('{"el":true}', $result);
    }

    public function testAssociativeArrayExpendStringKey()
    {
        $this->instanceConfig->set('expendObject', true);
        $result = $this->JSONformatter->createExpendedJson(['el' => true]);
        $this->assertMatchesRegularExpression('/\{\n {2,}"el" : true\n\}/', $result);
    }
}
