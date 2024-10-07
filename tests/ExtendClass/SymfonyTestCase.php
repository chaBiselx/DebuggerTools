<?php

namespace Test\ExtendClass;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Test\ExtendClass\BaseTestCase;
use Test\ObjectForTest\RoleEntity;
use Test\ObjectForTest\UserEntity;
use Symfony\Component\Cache\DoctrineProvider; // Permet d'adapter le cache pour Doctrine
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class SymfonyTestCase extends BaseTestCase
{
    protected static $entityManager;
    protected static $isDevMode = true;

    public function __construct()
    {
        parent::__construct();
        self::setEntityManager();
    }

    public static function setEntityManager()
    {
        // Connexion à une base SQLite en mémoire
        $connection = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        // Création de l'EntityManager
        $entityManager = EntityManager::create($connection, self::getConfig());

        self::$entityManager = $entityManager;

        $tool = new SchemaTool(self::$entityManager);
        $classes = array(
            self::$entityManager->getClassMetadata(RoleEntity::class),
            self::$entityManager->getClassMetadata(UserEntity::class),
        );
        $tool->dropSchema($classes);  // Empty out any current schema
        $tool->createSchema($classes);
    }

    /**
     * Creates a Doctrine configuration instance with the given entity paths and cache.
     * Crée une instance de configuration pour Doctrine
     *
     * @return \Doctrine\ORM\Configuration
     */
    public static function getConfig()
    {
        // Configuration des chemins vers les entités (par exemple src/Entity)
        $pathForEntity = realpath(__DIR__ . "/../ObjectForTest");

        // Configuration Doctrine avec Symfony Cache en PSR-6
        $cache = new DoctrineProvider(new ArrayAdapter());

        return Setup::createAnnotationMetadataConfiguration(
            [$pathForEntity],           // Chemin des entités
            self::$isDevMode,           // Mode de développement
            null,                       // Pas de chemin pour les proxies
            $cache,                     // Utilisation du cache
            false                       // Utiliser Simple Annotation Reader
        );
    }

    protected function getEntityManager()
    {
        return self::$entityManager;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return self::$entityManager->createQueryBuilder();
    }
}
