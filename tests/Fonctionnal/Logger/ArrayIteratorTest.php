<?php

declare(strict_types=1);

namespace Test\Fonctionnal\Logger;

use ArrayIterator;
use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\Entity\RoleEntity;

class ArrayIteratorTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testIteratorEmpty()
    {
        $this->Logger->logger(new ArrayIterator());
        $this->assertMatchesRegularExpression('/ : class \'ArrayIterator\' : /', $this->getContent());
    }

    public function testIteratorSimple()
    {
        $ArrayIterator = new ArrayIterator(['Value' => "data"]);
        $this->Logger->logger($ArrayIterator);
        $this->assertMatchesRegularExpression('/ : class \'ArrayIterator\' : \{"Value":"data"\}\n$/', $this->getContent());
    }

    public function testIteratorDouble()
    {
        $ArrayIterator = new ArrayIterator(['Value1' => "data", 'Value2' => "data"]);
        $this->Logger->logger($ArrayIterator);
        $this->assertMatchesRegularExpression('/ : class \'ArrayIterator\' : \{"Value1":"data","Value2":"data"\}\n$/', $this->getContent());
    }

    public function testIteratorObject()
    {
        $ArrayIterator = new ArrayIterator(['Value1' => new RoleEntity(), 'Value2' => new RoleEntity()]);
        $this->Logger->logger($ArrayIterator);
        $this->assertMatchesRegularExpression('/ : class \'ArrayIterator\' : \{"Value1":"\'Test\\\\ObjectForTest\\\\Entity\\\\RoleEntity\'","Value2":"\'Test\\\\ObjectForTest\\\\Entity\\\\RoleEntity\'"\}\n$/', $this->getContent());
    }

    public function testIteratorClosure()
    {
        $ArrayIterator = new ArrayIterator(['Value1' => function () {
            return 'test';
        }]);
        $this->Logger->logger($ArrayIterator);
        $this->assertMatchesRegularExpression('/ : class \'ArrayIterator\' : \{"Value1":"\'Closure\'"\}\n$/', $this->getContent());
    }
}
