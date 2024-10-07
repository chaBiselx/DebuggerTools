<?php

namespace Test\SubElement\Symfony\QueryBuilder;

use Debuggertools\Logger;
use Test\ExtendClass\SymfonyTestCase;

class QueryTest extends SymfonyTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testSimpleCase()
    {
        $query = self::$entityManager->createQuery('SELECT re.roleName FROM Test\ObjectForTest\RoleEntity re');
        $this->Logger->logger($query);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\Query\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_\.roleName AS roleName_0\s+FROM Role_User r0_/', $this->getContent());
    }
}
