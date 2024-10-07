<?php

namespace Test\SubElement\Symfony\QueryBuilder;

use Debuggertools\Logger;
use Test\ObjectForTest\RoleEntity;
use Test\ExtendClass\SymfonyTestCase;
use Debuggertools\Enumerations\OptionForInstanceEnum;

class QueryBuilderTest extends SymfonyTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger([OptionForInstanceEnum::PREFIX_HIDE => true]);
    }

    public function testSimpleCase()
    {
        $qb = self::$entityManager->createQueryBuilder();

        $qb->select('r')
            ->from(RoleEntity::class, 'r')
            ->getQuery();
        $this->Logger->logger($qb);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\QueryBuilder\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_.id AS id_0,\s+r0_.roleName AS roleName_1 \s+FROM Role_User r0_/', $this->getContent());
  
    }
}
