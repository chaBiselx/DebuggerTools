<?php

namespace Test\SubElement\Symfony\QueryBuilder;

use Debuggertools\Logger;
use Test\ObjectForTest\RoleEntity;
use Test\ExtendClass\SymfonyTestCase;
use Debuggertools\Enumerations\OptionForInstanceEnum;

class QueryTest extends SymfonyTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger([OptionForInstanceEnum::PREFIX_HIDE => true]);
    }

    public function testSelectSimple()
    {
        $query = self::$entityManager->createQuery('SELECT re.roleName FROM Test\ObjectForTest\RoleEntity re');
        $this->Logger->logger($query);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\Query\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_\.roleName AS roleName_0\s+FROM Role_User r0_/', $this->getContent());
    }

    
    public function testSelectSimpleWithParamString()
    {
        $query = self::$entityManager->createQuery('SELECT re.roleName FROM Test\ObjectForTest\RoleEntity re WHERE re.roleName = :roleName');
        $query->setParameter('roleName', 'admin');
        $this->Logger->logger($query);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\Query\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_\.roleName AS roleName_0 \s+FROM Role_User r0_\s+WHERE r0_\.roleName = \?/', $this->getContent());
        $this->assertEquals('["admin"]', $this->getLastLine());
    }

    public function testSelectSimpleWithParamInt()
    {
        $query = self::$entityManager->createQuery('SELECT re.roleName FROM Test\ObjectForTest\RoleEntity re WHERE re.id = :roleId');
        $query->setParameter('roleId', 10);
        $this->Logger->logger($query);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\Query\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_\.roleName AS roleName_0 \s+FROM Role_User r0_\s+WHERE r0_\.id = \?/', $this->getContent());
        $this->assertEquals('["10"]', $this->getLastLine());
    }

    public function testSelectSimpleWithParamObject()
    {
        $RoleEntity = (new RoleEntity())->setRoleName('admin')->setId(10);
        $query = self::$entityManager->createQuery('SELECT re.roleName FROM Test\ObjectForTest\RoleEntity re WHERE re.id = :RoleEntity');
        $query->setParameter('RoleEntity', $RoleEntity);
        $this->Logger->logger($query);
        $this->assertMatchesRegularExpression('/class \'Doctrine\\\\ORM\\\\Query\' :/', $this->getContent());
        $this->assertMatchesRegularExpression('/SELECT\s+r0_\.roleName AS roleName_0 \s+FROM Role_User r0_\s+WHERE r0_\.id = \?/', $this->getContent());
        $this->assertMatchesRegularExpression('/\n\["\'Test\\\\ObjectForTest\\\\RoleEntity\'"\]\n/', $this->getContent());
        $this->assertEquals('\'Test\ObjectForTest\RoleEntity\' => {"permissions":[],"->getId":10,"->getRoleName":"admin"}', $this->getLastLine());
    }
}
