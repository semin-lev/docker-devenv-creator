<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Command;


use DefaultConfigurationFactory\ProjectFactory;
use PHPDocker\Generator\Factory;
use PHPDocker\PhpExtension\AvailableExtensionsFactory;
use PHPDocker\Project\ServiceOptions\Application;
use PHPDocker\Project\ServiceOptions\Elasticsearch;
use PHPDocker\Project\ServiceOptions\MariaDB;
use PHPDocker\Project\ServiceOptions\MySQL;
use PHPDocker\Project\ServiceOptions\Php;
use PHPDocker\Project\ServiceOptions\Postgres;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateDevenvCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('project_name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('extract_to_dir', 'd', InputOption::VALUE_OPTIONAL, 'Extract to dir', './');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = Factory::create();
        $project = (new ProjectFactory)->getDefaultConfiguration();

        $project->setName($input->getArgument('project_name'));


        /** @var QuestionHelper $quest */
        $quest = $this->getHelper('question');

        // get app type
        $applicationType = $quest->ask($input, $output, new ChoiceQuestion(
            'Choice application type. The default is '.$project->getApplicationOptions()->getApplicationType(),
            Application::getChoices(),
            $project->getApplicationOptions()->getApplicationType()
        ));
        $project->getApplicationOptions()->setApplicationType($applicationType);
        //end get app type

        //get app port
        $portQuest = new Question(
            sprintf("Select external port, default is %d: ", $project->getBasePort()),
            $project->getBasePort()
        );
        $portQuest->setNormalizer(function ($value){
            return (int)$value;
        });
        $portQuest->setValidator(function ($port){
            if ($port<=1000) {
                throw new \RuntimeException(sprintf("The port must be more than 1000"));
            }
            if ($port>9999) {
                throw new \RuntimeException(sprintf("The port must be lest than 9999"));
            }
            return $port;
        });
        $port = $quest->ask($input, $output, $portQuest);
        $project->setBasePort($port);
        // end get port

        // get php version
        $phpVersion = $quest->ask($input, $output, new ChoiceQuestion(
            'Select php version, default is '.$project->getPhpOptions()->getVersion(),
            Php::getSupportedVersions(),
            $project->getPhpOptions()->getVersion()
        ));
        $project->getPhpOptions()->setVersion($phpVersion);
        // end get php version

        // get php extensions
        $extension = AvailableExtensionsFactory::create($phpVersion);

        if ($quest->ask($input, $output, new ConfirmationQuestion(
            sprintf(
                'Do you want to disable some default extensions? They are %s. Y/n (n): ',
                implode(", ", $project->getPhpOptions()->getExtensionNames())
            ),
            false
        ))) {
            $forDisableQuestion = new ChoiceQuestion(
                'Pls, choose extensions for disable',
                $project->getPhpOptions()->getExtensionNames()
            );
            $forDisableQuestion->setMultiselect(true);
            $forDisableExtensions = $quest->ask($input, $output, $forDisableQuestion);

            foreach ($forDisableExtensions as $extName) {
                $project->getPhpOptions()->removeExtension($extName);
            }
        }

        $availableToEnable = array_diff(array_keys($extension->getAll()), $project->getPhpOptions()->getExtensionNames());

        if ($quest->ask($input, $output, new ConfirmationQuestion(
            sprintf(
                'Do you want to enable some additional extensions? %s are available. Y/n (n):',
                implode(", ", $availableToEnable)
            ),
            false
        ))) {
            $extensionsQuestion = new ChoiceQuestion(
                'Pls, choose extensions for enable',
                $availableToEnable
            );
            $extensionsQuestion->setMultiselect(true);
            $values = $quest->ask($input, $output, $extensionsQuestion);
            foreach ($values as $extName) {
                $project->getPhpOptions()->addExtension($extension->getPhpExtension($extName));
            }
        }
        // end get php extensions

        // get mysql
        $mysql = $quest->ask($input, $output, new ConfirmationQuestion(
            'Do you want to enable mysql? (y/n): ',
            $project->getMysqlOptions()->isEnabled()
        ));
        if ($mysql) {
            $project->getMysqlOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select MySQL version, default is '.$project->getMysqlOptions()->getVersion(),
                MySQL::getChoices(),
                $project->getMysqlOptions()->getVersion()
            ));
            $project->getMysqlOptions()->setVersion($version);

            $rootPwd = $quest->ask($input, $output, new Question(
                sprintf("Mysql root password (default %s): ", $project->getMysqlOptions()->getRootPassword()),
                $project->getMysqlOptions()->getRootPassword()
            ));
            $project->getMysqlOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question(
                sprintf("Application database name (default %s): ", $project->getMysqlOptions()->getDatabaseName()),
                $project->getMysqlOptions()->getDatabaseName())
            );
            $project->getMysqlOptions()->setDatabaseName($dbName);

            $userName = $quest->ask($input, $output, new Question(
                sprintf("Application user name (default %s): ", $project->getMysqlOptions()->getUsername()),
                $project->getMysqlOptions()->getUsername()
            ));
            $project->getMysqlOptions()->setUsername($userName);

            $usrPwd = $quest->ask($input, $output, new Question(
                sprintf("%s password (default %s): ", $userName, $project->getMysqlOptions()->getPassword()),
                $project->getMysqlOptions()->getPassword()
            ));
            $project->getMysqlOptions()->setPassword($usrPwd);
        }
        // end get mysql


        // get mariadb
        $mariadb = $quest->ask($input, $output, new ConfirmationQuestion(
            'Do you want to enable MariaDb? (y/n): ',
            $project->getMariadbOptions()->isEnabled()
        ));
        if ($mariadb) {
            $project->getMariadbOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select MariaDb version, default is '.$project->getMariadbOptions()->getVersion(),
                MariaDB::getChoices(),
                $project->getMariadbOptions()->getVersion()
            ));
            $project->getMariadbOptions()->setVersion($version);

            $rootPwd = $quest->ask($input, $output, new Question(
                sprintf("MariaDB root password (default %s): ", $project->getMariadbOptions()->getRootPassword()),
                $project->getMariadbOptions()->getRootPassword()
            ));
            $project->getMariadbOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question(
                    sprintf("Application MariaDB database name (default %s): ", $project->getMariadbOptions()->getDatabaseName()),
                    $project->getMariadbOptions()->getDatabaseName())
            );
            $project->getMariadbOptions()->setDatabaseName($dbName);

            $userName = $quest->ask($input, $output, new Question(
                sprintf("Application MariaDB user name (default %s): ", $project->getMariadbOptions()->getUsername()),
                $project->getMariadbOptions()->getUsername()
            ));
            $project->getMariadbOptions()->setUsername($userName);

            $usrPwd = $quest->ask($input, $output, new Question(
                sprintf("MariaDB %s password (default %s): ", $userName, $project->getMariadbOptions()->getPassword()),
                $project->getMariadbOptions()->getPassword()
            ));
            $project->getMariadbOptions()->setPassword($usrPwd);
        }
        // end get mariadb

        // get postgres
        $postgres = $quest->ask($input, $output, new ConfirmationQuestion(
            'Do you want to enable Postgres? (y/n): ',
            $project->getPostgresOptions()->isEnabled()
        ));
        if ($postgres) {
            $project->getPostgresOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select Postgres version, default is '.$project->getPostgresOptions()->getVersion(),
                Postgres::getChoices(),
                $project->getPostgresOptions()->getVersion()
            ));
            $project->getPostgresOptions()->setVersion($version);

            $userName = $quest->ask($input, $output, new Question(
                sprintf("Root postgres user name (default %s): ", $project->getPostgresOptions()->getRootUser()),
                $project->getPostgresOptions()->getRootUser())
            );
            $project->getPostgresOptions()->setRootUser($userName);

            $rootPwd = $quest->ask($input, $output, new Question(
                sprintf("Postgres root password (default %s): ", $project->getPostgresOptions()->getRootPassword()),
                $project->getPostgresOptions()->getRootPassword()
            ));
            $project->getPostgresOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question(
                sprintf("Application Postgres database name (default %s): ", $project->getPostgresOptions()->getDatabaseName()),
                $project->getPostgresOptions()->getDatabaseName()
            ));
            $project->getPostgresOptions()->setDatabaseName($dbName);
        }
        // end get postgres

        //get elasticsearch
        $elastic = $quest->ask($input, $output, new ConfirmationQuestion(
            'Do you want to enable ElasticSearch? (y/n): ',
            $project->getElasticsearchOptions()->isEnabled()
        ));

        if ($elastic) {
            $project->getElasticsearchOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select ElasticSearch version, default is '.$project->getElasticsearchOptions()->getVersion(),
                Elasticsearch::getChoices(),
                $project->getElasticsearchOptions()->getVersion()
            ));
            $project->getElasticsearchOptions()->setVersion($version);
        }
        //end get elasticsearch

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Memcache? (y/n): ', $project->getMemcachedOptions()->isEnabled()))) {
            $project->getMemcachedOptions()->setEnabled(true);
        }

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Redis? (y/n): ', $project->getRedisOptions()->isEnabled()))) {
            $project->getRedisOptions()->setEnabled(true);
        }

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Mailhog? (y/n): ', $project->getMailhogOptions()->isEnabled()))) {
            $project->getMailhogOptions()->setEnabled(true);
        }

        $zip = $generator->generate($project);

        $archive = new \ZipArchive();

        try {
            $archive->open($zip->getTmpFilename());
            $archive->extractTo($input->getOption('extract_to_dir'));
        } finally {
            $archive->close();
        }

        $output->writeln("End");

    }
}