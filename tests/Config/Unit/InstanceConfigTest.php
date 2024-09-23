<?php

namespace Test\Config\Unit;

use Test\ExtendClass\BaseTestCase;
use Debuggertools\Config\InstanceConfig;

class InstanceConfigTest extends BaseTestCase
{

    public function testSetterGetterSimple()
    {
        $instanceConfig = new InstanceConfig();
        $instanceConfig->set('key', 'value');
        $this->assertEquals('value', $instanceConfig->get('key'));
    }

    public function testMonoInstance()
    {
        $instanceConfig1 = new InstanceConfig();
        $instanceConfig1->set('key', 'same instance');
        $instanceConfig1 = null;

        $instanceConfig2 = new InstanceConfig();
        $this->assertNull($instanceConfig1);
        $this->assertEquals('same instance', $instanceConfig2->get('key'));
    }

    public function testReset()
    {
        $instanceConfig = new InstanceConfig();
        $instanceConfig->set('key', 'same instance');

        $this->assertEquals('same instance', $instanceConfig->get('key'));
        $instanceConfig->reset();
        $this->assertNull($instanceConfig->get('key'));
    }

    public function testSetterGetterMultiple()
    {
        $instanceConfig = new InstanceConfig();
        $instanceConfig->set('key1', 'value1');
        $instanceConfig->set('key2', 'value2');
        $this->assertEquals('value1', $instanceConfig->get('key1'));
        $this->assertEquals('value2', $instanceConfig->get('key2'));
    }

    public function testTypeSetted()
    {
        $instanceConfig = new InstanceConfig();
        $instanceConfig->set('string', 'a string');
        $instanceConfig->set('boolTrue', true);
        $instanceConfig->set('boolFalse', false);
        $instanceConfig->set('int', 15);
        $instanceConfig->set('float', 12.165);
        $this->assertEquals('a string', $instanceConfig->get('string'));
        $this->assertEquals(true, $instanceConfig->get('boolTrue'));
        $this->assertEquals(false, $instanceConfig->get('boolFalse'));
        $this->assertEquals(15, $instanceConfig->get('int'));
        $this->assertEquals(12.165, $instanceConfig->get('float'));
    }
}
