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


use Command\QuestionFactory\AbstractMysqlQuestionFactory;
use Command\QuestionFactory\ApplicationQuestionFactory;
use Command\QuestionFactory\ElasticSearchQuestionFactory;
use Command\QuestionFactory\MailhogQuestionFactory;
use Command\QuestionFactory\MariaDbQuestionFactory;
use Command\QuestionFactory\MemcacheQuestionFactory;
use Command\QuestionFactory\MysqlQuestionFactory;
use Command\QuestionFactory\PhpQuestionFactory;
use Command\QuestionFactory\PostgresQuestionFactory;
use Command\QuestionFactory\RedisQuestionFactory;
use DefaultConfigurationFactory\ProjectFactory;
use PHPDocker\Generator\Factory;
use PHPDocker\Project\ServiceOptions\AbstractMySQL;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDevenvCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('project_name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('extract_to_dir', 'd', InputOption::VALUE_OPTIONAL, 'Extract to dir', './');
        $this->addOption('zip', 'z', InputOption::VALUE_NONE, 'Save zip archive');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = Factory::create();
        $project = (new ProjectFactory)->getDefaultConfiguration();
        $project->setName($input->getArgument('project_name'));

        $appQuestFactory           = new ApplicationQuestionFactory($project);
        $phpQuestFactory           = new PhpQuestionFactory($project);
        $mysqlQuestFactory         = new MysqlQuestionFactory($project);
        $mariadbQuestFactory       = new MariaDbQuestionFactory($project);
        $postgresQuestFactory      = new PostgresQuestionFactory($project);
        $elasticSearchQuestFactory = new ElasticSearchQuestionFactory($project);

        /** @var QuestionHelper $quest */
        $quest = $this->getHelper('question');

        // get app type
        $applicationType = $quest->ask($input, $output, $appQuestFactory->getAppTypeQuestion());
        $project->getApplicationOptions()->setApplicationType($applicationType);
        //end get app type

        //get app port
        $port = $quest->ask($input, $output, $appQuestFactory->getPortQuestion());
        $project->setBasePort($port);
        // end get port

        // get php version
        $phpVersion = $quest->ask($input, $output, $phpQuestFactory->getVersionQuestion());
        $project->getPhpOptions()->setVersion($phpVersion);
        // end get php version

        // get php extensions
        if ($quest->ask($input, $output, $phpQuestFactory->getConfirmDisableExtensionQuestion())) {
            $forDisableExtensions = $quest->ask($input, $output, $phpQuestFactory->getForDisableQuestion());
            $project->getPhpOptions()->removeExtensions($forDisableExtensions);
        }

        if ($quest->ask($input, $output, $phpQuestFactory->getConfirmEnableExtensionQuestion())) {
            $values = $quest->ask($input, $output, $phpQuestFactory->getForEnableQuestion());
            $project->getPhpOptions()->addExtensionsByName($values);
        }
        // end get php extensions

        $sqlDbs = [
            [$mysqlQuestFactory, $project->getMysqlOptions()],
            [$mariadbQuestFactory, $project->getMariadbOptions()]
        ];

        foreach ($sqlDbs as $db) {
            /**
             * @var $dbQuestFactory \Command\QuestionFactory\AbstractMysqlQuestionFactory
             * @var $dbOptions AbstractMySQL
             */
            list($dbQuestFactory, $dbOptions) = $db;

            if ($quest->ask($input, $output, $dbQuestFactory->getIsEnabledQuestion())) {
                $dbOptions->setEnabled(true);

                $version = $quest->ask($input, $output, $dbQuestFactory->getVersionQuestion());
                $dbOptions->setVersion($version);

                $rootPwd = $quest->ask($input, $output, $dbQuestFactory->getRootPasswordQuestion());
                $dbOptions->setRootPassword($rootPwd);

                $dbName = $quest->ask($input, $output, $dbQuestFactory->getDbNameQuestion());
                $dbOptions->setDatabaseName($dbName);

                $userName = $quest->ask($input, $output, $dbQuestFactory->getDbUserNameQuestion());
                $dbOptions->setUsername($userName);

                $usrPwd = $quest->ask($input, $output, $dbQuestFactory->getDbUserPasswordQuestion());
                $dbOptions->setPassword($usrPwd);
            } else {
                $dbOptions->setEnabled(false);
            }
        }

        // get postgres
        if ($quest->ask($input, $output, $postgresQuestFactory->getIsEnabledQuestion())) {
            $project->getPostgresOptions()->setEnabled(true);

            $version = $quest->ask($input, $output, $postgresQuestFactory->getVersionQuestion());
            $project->getPostgresOptions()->setVersion($version);

            $userName = $quest->ask($input, $output, $postgresQuestFactory->getDbUserNameQuestion());
            $project->getPostgresOptions()->setRootUser($userName);

            $rootPwd = $quest->ask($input, $output, $postgresQuestFactory->getDbUserPasswordQuestion());
            $project->getPostgresOptions()->setRootPassword($rootPwd);

            $dbName = $quest->ask($input, $output, $postgresQuestFactory->getDbNameQuestion());
            $project->getPostgresOptions()->setDatabaseName($dbName);
        } else {
            $project->getPostgresOptions()->setEnabled(false);
        }
        // end get postgres

        //get elasticsearch
        if ($quest->ask($input, $output, $elasticSearchQuestFactory->getIsEnabledQuestion())) {
            $project->getElasticsearchOptions()->setEnabled(true);
            $version = $quest->ask($input, $output, $elasticSearchQuestFactory->getVersionQuestion());
            $project->getElasticsearchOptions()->setVersion($version);
        } else {
            $project->getElasticsearchOptions()->setEnabled(false);
        }
        //end get elasticsearch

        $project
            ->getMemcachedOptions()
            ->setEnabled($quest->ask($input, $output, (new MemcacheQuestionFactory($project))->getIsEnabledQuestion()))
        ;

        $project
            ->getRedisOptions()
            ->setEnabled($quest->ask($input, $output, (new RedisQuestionFactory($project))->getIsEnabledQuestion()))
        ;

        $project
            ->getMailhogOptions()
            ->setEnabled($quest->ask($input, $output, (new MailhogQuestionFactory($project))->getIsEnabledQuestion()))
        ;

        $zip = $generator->generate($project);

        if ($input->getOption('zip')) {
            copy($zip->getTmpFilename(), $input->getOption('extract_to_dir').'/'.$zip->getFilename());
        } else {
            $archive = new \ZipArchive();

            try {
                $archive->open($zip->getTmpFilename());
                $archive->extractTo($input->getOption('extract_to_dir'));
            } finally {
                $archive->close();
            }
        }

        $output->writeln("End");

    }
}