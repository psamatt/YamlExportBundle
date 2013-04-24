<?php

namespace Psamatt\YamlExportBundle\Tests\Command;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

abstract class FunctionalTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
    * An array of classes to get class metadata for
    * @var classes
    */
    protected $classes;

    public function getConnection()
    {
        $classMetadata = array();
        $this->em = $this->createEntityManager();
        $conn = $this->em->getConnection();
        $pdo = $conn->getWrappedConnection();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);

        if (count($this->classes) > 0) {
            foreach ($this->classes as $class) {
                $classMetadata[] = $this->em->getClassMetadata($class);
            }
        } else {
            throw new \Doctrine\ORM\ORMException("No associated classes not yet implemented.");
        }

        $schemaTool->createSchema($classMetadata);

        return $this->createDefaultDBConnection($pdo, $conn->getDatabase());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return Doctrine\ORM\EntityManager
     */
    protected function createEntityManager()
    {
        AnnotationRegistry::registerFile(__DIR__ . "/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");

        $annotationDriver = new AnnotationDriver(new AnnotationReader(), __DIR__ . "/Entity");

        $config = new \Doctrine\ORM\Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('Psamatt\Proxies');
        $config->setMetadataCacheImpl($cache = new \Doctrine\Common\Cache\ArrayCache());
        $config->setMetadataDriverImpl($annotationDriver);
        $config->setQueryCacheImpl($cache);
        $config->setEntityNamespaces(array(
            'PsamattYamlExportBundle' => 'Psamatt\\YamlExportBundle\\Tests\\Command\\Entity'
        ));

        $connectionOptions = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        return \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
    }
}
