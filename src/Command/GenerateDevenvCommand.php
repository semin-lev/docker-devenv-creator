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


use PHPDocker\Generator\Factory;
use PHPDocker\PhpExtension\Php56AvailableExtensions;
use PHPDocker\PhpExtension\Php70AvailableExtensions;
use PHPDocker\PhpExtension\Php71AvailableExtensions;
use PHPDocker\Project\Project;
use PHPDocker\Project\ServiceOptions\Application;
use PHPDocker\Project\ServiceOptions\Elasticsearch;
use PHPDocker\Project\ServiceOptions\MariaDB;
use PHPDocker\Project\ServiceOptions\MySQL;
use PHPDocker\Project\ServiceOptions\Php;
use PHPDocker\Project\ServiceOptions\Postgres;
use Slugifier\BaseSlugifier;
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
        $project = new Project(new BaseSlugifier());

        $project->setName($input->getArgument('project_name'));


        /** @var QuestionHelper $quest */
        $quest = $this->getHelper('question');

        // get app type
        $applicationType = $quest->ask($input, $output, new ChoiceQuestion(
            'Choice application type. The default is '.Application::APPLICATION_TYPE_SYMFONY,
            Application::getChoices(),
            Application::APPLICATION_TYPE_SYMFONY
        ));
        $project->getApplicationOptions()->setApplicationType($applicationType);
        //end get app type

        //get app port
        $portQuest = new Question("Select external port, default is 8000: ", 8000);
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
        $php = $quest->ask($input, $output, new ChoiceQuestion(
            'Select php version, default is '.Php::PHP_VERSION_71,
            Php::getSupportedVersions(),
            PHP::PHP_VERSION_71
        ));
        $project->getPhpOptions()->setVersion($php);
        // end get php version

        // get php extensions
        $extension = null;
        switch ($php) {
            case Php::PHP_VERSION_56:
                $extension = new Php56AvailableExtensions();
                break;
            case Php::PHP_VERSION_70:
                $extension = new Php70AvailableExtensions();
                break;
            case Php::PHP_VERSION_71:
                $extension = new Php71AvailableExtensions();
                break;
            default:
                throw new \RuntimeException(sprintf("Unsupported php extension version: %s", $php));
        }
        $extensionsQuestion = new ChoiceQuestion('Select php extensions', array_keys($extension->getAll()));
        $extensionsQuestion->setMultiselect(true);
        $values = $quest->ask($input, $output, $extensionsQuestion);
        foreach ($values as $extName) {
            $project->getPhpOptions()->addExtension($extension->getPhpExtension($extName));
        }
        // end get php extensions

        // get mysql
        $mysql = $quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable mysql? (y/n): '));
        if ($mysql) {
            $project->getMysqlOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select MySQL version, default is '.MySQL::VERSION_57,
                MySQL::getChoices(),
                MySQL::VERSION_57
            ));
            $project->getMysqlOptions()->setVersion($version);

            $rootPwd = $quest->ask($input, $output, new Question("Mysql root password (default 123): ", '123'));
            $project->getMysqlOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question("Application database name (default db): ", 'db'));
            $project->getMysqlOptions()->setDatabaseName($dbName);

            $userName = $quest->ask($input, $output, new Question("Application user name (default db_user): ", 'db_user'));
            $project->getMysqlOptions()->setUsername($userName);

            $usrPwd = $quest->ask($input, $output, new Question($userName." password (default 123): ", '123'));
            $project->getMysqlOptions()->setPassword($usrPwd);
        }
        // end get mysql


        // get mariadb
        $mariadb = $quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable MariaDb? (y/n): ', false));
        if ($mariadb) {
            $project->getMariadbOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select MariaDb version, default is '.MariaDB::VERSION_101,
                MariaDB::getChoices(),
                MariaDB::VERSION_101
            ));
            $project->getMariadbOptions()->setVersion($version);

            $rootPwd = $quest->ask($input, $output, new Question("MariaDb root password (default 123): ", '123'));
            $project->getMariadbOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question("Application MariaDb database name (default db): ", 'db'));
            $project->getMariadbOptions()->setDatabaseName($dbName);

            $userName = $quest->ask($input, $output, new Question("Application MariaDb user name (default db_user): ", 'db_user'));
            $project->getMariadbOptions()->setUsername($userName);

            $usrPwd = $quest->ask($input, $output, new Question($userName." password (default 123): ", '123'));
            $project->getMariadbOptions()->setPassword($usrPwd);
        }
        // end get mariadb

        // get postgres
        $postgres = $quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Postgres? (y/n): ', false));
        if ($postgres) {
            $project->getPostgresOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, new ChoiceQuestion(
                'Select Postgres version, default is '.Postgres::VERSION_96,
                Postgres::getChoices(),
                Postgres::VERSION_96
            ));
            $project->getPostgresOptions()->setVersion($version);

            $userName = $quest->ask($input, $output, new Question("Root postgres user name (default root): ", 'root'));
            $project->getPostgresOptions()->setRootUser($userName);

            $rootPwd = $quest->ask($input, $output, new Question("Postgres root password (default 123): ", '123'));
            $project->getPostgresOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, new Question("Application Postgres database name (default db): ", 'db'));
            $project->getPostgresOptions()->setDatabaseName($dbName);
        }
        // end get postgres

        //get elasticsearch
        $elastic = $quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable ElasticSearch? (y/n): ', false));
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

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Memcache? (y/n): '))) {
            $project->getMemcachedOptions()->setEnabled(true);
        }

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Redis? (y/n): ', false))) {
            $project->getRedisOptions()->setEnabled(true);
        }

        if ($quest->ask($input, $output, new ConfirmationQuestion('Do you want to enable Mailhog? (y/n): ', false))) {
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