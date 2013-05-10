<?php

namespace Psamatt\YamlExportBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;
use Mockery as m;

use Psamatt\YamlExportBundle\Command\YamlExportCommand;

class YamlExportCommandTest extends FunctionalTestCase
{
    /**
     * The command to test
     *
     * @access private
     */
    private $command;

    /**
     * Yaml object to count results
     *
     * @access private
     */
    private $yaml;

    /**
     * Initial constructor telling the base test class what classes to use for this test
     *
     */
    public function __construct()
    {
        $this->classes = array(
            'PsamattYamlExportBundle:BlogPostCategory',
            'PsamattYamlExportBundle:BlogPost',
        );
    }

    /**
     * Test a valid query returns YAML
     *
     */
    public function testValidSQLQuery()
    {

        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT * FROM blog_posts', '--sql' => true)
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(3, $result['blog_posts']);
    }

    /**
     * Test a valid query returns YAML
     *
     */
    public function testValidSQLWithoutSqlOptionQuery()
    {

        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT * FROM blog_posts')
        );

        $this->assertRegExp('/error/i', $commandTester->getDisplay());
    }

    /**
     * Test an invalid query
     *
     */
    public function testInvalidSQLQuery()
    {

        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => '* FROM blog_posts', '--sql' => true)
        );

        $this->assertRegExp('/error/i', $commandTester->getDisplay());
    }

    /**
     * Test a valid DQL with bundle entity query
     *
     */
    public function testValidDQLWithBundleEntityQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM PsamattYamlExportBundle:BlogPost bp')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(3, $result['blog_posts']);
    }

    /**
     * Test a valid DQL with bundle entity query containing a join
     *
     */
    public function testValidDQLWithBundleEntityLeftJoinQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM PsamattYamlExportBundle:BlogPost bp LEFT JOIN bp.category c')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(3, $result['blog_posts']);
    }

    /**
     * Test a valid DQL with bundle entity query
     *
     */
    public function testValidDQLWithBundleEntityWhereClauseQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM PsamattYamlExportBundle:BlogPost bp WHERE bp.id = 1')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(1, $result['blog_posts']);
    }

    /**
     * Test a valid DQL with bundle entity query containing a join and where clause
     *
     */
    public function testValidDQLWithBundleEntityLeftJoinWithWhereClauseQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM PsamattYamlExportBundle:BlogPost bp LEFT JOIN bp.category c WHERE c.id = 1')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(1, $result['blog_posts']);
    }

    /**
     * Test a valid DQL with bundle entity query containing a join and where clause
     *
     */
    public function testValidDQLWithBundleEntityWithSubQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM PsamattYamlExportBundle:BlogPost bp WHERE bp.category_id = (SELECT c.id FROM PsamattYamlExportBundle:BlogPostCategory c WHERE c.id = 1)')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(1, $result['blog_posts']);
    }

    /**
     * Test a valid DQL with namespaced entity query
     *
     */
    public function testValidDQLWithNamespacedEntityQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT bp FROM Psamatt\YamlExportBundle\Tests\Command\Entity\BlogPost bp')
        );

        $result = $this->yaml->parse($commandTester->getDisplay());

        $this->assertCount(3, $result['blog_posts']);
    }

    /**
     * Test an invalid DQL with incorrect entity query
     *
     */
    public function testInvalidDQLEntityQuery()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(
            array('command' => $this->command->getName(), 'query' => 'SELECT i FROM MyIncorrectEntity i')
        );

        $this->assertRegExp('/error/i', $commandTester->getDisplay());
    }

    /**
    * {@inheritdoc}
    */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__."/Entity/_files/seed.yml");
    }

    /**
     * Setup the unit test and the DI container
     */
    public function setUp()
    {
        parent::setUp();
        
        $container = m::mock('Symfony\Component\DependencyInjection\Container');
        $container
            ->shouldReceive('get')
            ->once()
            ->with('doctrine.orm.entity_manager')
            ->andReturn($this->getEntityManager());

        $application = new Application();
        $application->add(new YamlExportCommand());

        $this->command = $application->find('YamlExport:data-dump');
        $this->command->setContainer($container);

        $this->yaml = new Parser();
    }
}
