<?php

namespace Psamatt\YamlExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
* Dump out database rows into yaml formatting for use in DBUnit testing
*
*/
class YamlExportCommand extends ContainerAwareCommand
{
    /**
    * Configure the command
    *
    * Argument :: query SQL or DQL
    * Option :: sql
    */
    protected function configure()
    {
        $this
            ->setName('psamatt:yaml-export:dump')
            ->setDescription('Database entity exporter of one / many entities')
            ->addArgument('query', InputArgument::REQUIRED, 'The query to run')
            ->addOption('sql', null, InputOption::VALUE_NONE, 'If set, then we assume we are trying to run as native sql')
        ;
    }

    /**
     * Execute database query and export all rows into Yaml format
     *
     * @param InputInterface  $input
     * @param OutputInterface $result
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $returnString = "";
        $query = $input->getArgument('query');
        $isSql = $input->getOption('sql');

        if (!preg_match('/\bfrom\b\s*([\w:\\\]+)/i', $query, $matches)) {
            $output->writeln("<error>ERROR: Statement invalid - are you sure this valid DQL / SQL?</error>");

            return 0;
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        if (!$isSql) {
            if (!$em->getMetadataFactory()->hasMetadataFor($matches[1])) {
                $output->writeln("<error>ERROR: Entity {$matches[1]} does not exist - are you sure you specified the right entity path?</error>");

                return 0;
            }

            $tableName = $em->getClassMetadata($matches[1])->getTableName();
        } else {
            $tableName = $matches[1];
        }

        $returnString = $tableName .":". PHP_EOL;

        try {

            if ($isSql === true) {
                $stmt = $em->getConnection()->prepare($query);
                $stmt->execute();
                $rows = $stmt->fetchAll();
            } else {
                $query = $em->createQuery($query);
                $rows = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            }

            // loop over the rows
            foreach ($rows as $row) {
                // loop over each field
                $returnString .= "  -\n";
                foreach ($row as $fieldName => $fieldValue) {
                    $literalFlag = "";

                    if (is_object($fieldValue)) {
                         if ($fieldValue instanceof \DateTime) {
                             $fieldValue = $fieldValue->format('Y-m-d H:i:s');
                         }
                     } elseif (is_string($fieldValue)) {

                         // Do have any newlines or line feeds?
                        $literalFlag = (strpos($fieldValue, "\r") !== FALSE || strpos($fieldValue, "\n") !== FALSE) ? "| " : "";
                         $fieldValue = '"' . str_replace('"','\"',$fieldValue) . '"';
                     }

                    // Output the key/value pair
                    $returnString .= "    {$fieldName}: {$literalFlag}{$fieldValue}\n";
                }
            }

            // write the output
            $output->writeln($returnString);

            return 1;

        } catch (\Doctrine\DBAL\DBALException $e) {
            $output->writeln("<error>ERROR: {$e->getMessage()}</error>");

            return 0;
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            $output->writeln("<error>ERROR: {$e->getMessage()}</error>");

            return 0;
        } catch (\PDOException $e) {
            $output->writeln("<error>ERROR: {$e->getMessage()}</error>");

            return 0;
        }
    }
}
