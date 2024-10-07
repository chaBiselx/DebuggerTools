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

    public function testSelectSimple()
    {
        $qb = self::$entityManager->createQueryBuilder();

        $qb->select('r')
            ->from(RoleEntity::class, 'r')
            ->getQuery();
        $this->Logger->logger($qb);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\QueryBuilder\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_.id AS id_0,\s+r0_.roleName AS roleName_1 \s+FROM Role_User r0_/', $this->getContent());
    }

    public function testSimpleWithParamString()
    {
        $qb = self::$entityManager->createQueryBuilder();

        $qb->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.roleName = :roleName')
            ->setParameter('roleName', 'admin')
            ->getQuery();
        $this->Logger->logger($qb);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\QueryBuilder\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_.id AS id_0,\s+r0_.roleName AS roleName_1 \s+FROM Role_User r0_/', $this->getContent());
        $this->assertEquals('["admin"]', $this->getLastLine());
    }

    public function testSelectSimpleWithParamInt()
    {
        $qb = self::$entityManager->createQueryBuilder();
        $qb->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.id = :roleId')
            ->setParameter('roleId', 10)
            ->getQuery();
        $this->Logger->logger($qb);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\QueryBuilder\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_.id AS id_0,\s+r0_\.roleName AS roleName_1 \s+FROM Role_User r0_\s+WHERE r0_\.id = \?/', $this->getContent());
        $this->assertEquals('["10"]', $this->getLastLine());
    }

    public function testSelectSimpleWithParamObject()
    {
        $RoleEntity = (new RoleEntity())->setRoleName('admin')->setId(10);
        $qb = self::$entityManager->createQueryBuilder();
        $qb->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.id = :RoleEntity')
            ->setParameter('RoleEntity', $RoleEntity)
            ->getQuery();
        $this->Logger->logger($qb);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\QueryBuilder\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_.id AS id_0,\s+r0_\.roleName AS roleName_1 \s+FROM Role_User r0_\s+WHERE r0_\.id = \?/', $this->getContent());
        $this->assertMatchesRegularExpression('/\n\["\'Test\\\\ObjectForTest\\\\RoleEntity\'"\]\n/', $this->getContent());
        $this->assertEquals('\'Test\ObjectForTest\RoleEntity\' => {"permissions":[],"->getId":10,"->getRoleName":"admin"}', $this->getLastLine());

    }
}
