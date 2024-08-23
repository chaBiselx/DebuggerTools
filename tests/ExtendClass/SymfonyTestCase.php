<?php

namespace Test\ExtendClass;

use Test\ExtendClass\BaseTestCase;

abstract class SymfonyTestCase extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();

        // Création de l'EntityManager
        $this->entityManager = null;
    }

    protected function getQueryBuilder() //: Doctrine\ORM\QueryBuilder

    {
        return 'TODO'; // $this->entityManager->createQueryBuilder();
    }
}
