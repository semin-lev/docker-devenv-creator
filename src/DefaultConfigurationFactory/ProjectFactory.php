<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DefaultConfigurationFactory;


use PHPDocker\PhpExtension\AvailableExtensionsFactory;
use PHPDocker\Project\Project;
use PHPDocker\Project\ServiceOptions\Application;
use PHPDocker\Project\ServiceOptions\Elasticsearch;
use PHPDocker\Project\ServiceOptions\Mailhog;
use PHPDocker\Project\ServiceOptions\MariaDB;
use PHPDocker\Project\ServiceOptions\Memcached;
use PHPDocker\Project\ServiceOptions\MySQL;
use PHPDocker\Project\ServiceOptions\Php;
use PHPDocker\Project\ServiceOptions\Postgres;
use PHPDocker\Project\ServiceOptions\Redis;
use Slugifier\BaseSlugifier;

class ProjectFactory implements DefaultConfigurationFactoryInterface
{
    private $appConfigFactory;

    private $phpConfigFactory;

    private $mysqlConfigFactory;

    private $mariaDbConfigFactory;

    private $postgresConfigFactory;

    private $elasticSearchConfigFactory;

    private $memcachedConfigFactory;

    private $redisConfigFactory;

    private $mailHogConfigFactory;

    /**
     * ProjectFactory constructor.
     */
    public function __construct()
    {
        $this->appConfigFactory           = new ApplicationDefaultConfigurationFactory();
        $this->phpConfigFactory           = new PhpDefaultConfigurationFactory();
        $this->mysqlConfigFactory         = new MySqlDefaultConfigurationFactory(MySqlDefaultConfigurationFactory::MYSQL);
        $this->mariaDbConfigFactory       = new MySqlDefaultConfigurationFactory(MySqlDefaultConfigurationFactory::MARIADB);
        $this->postgresConfigFactory      = new PostgresDefaultConfigurationFactory();
        $this->elasticSearchConfigFactory = new ElasticSearchDefaultConfigurationFactory();
        $this->memcachedConfigFactory     = new MemcacheDefaultConfigurationFactory();
        $this->redisConfigFactory         = new RedisDefaultConfigurationFactory();
        $this->mailHogConfigFactory       = new MailhogDefaultConfigurationFactory();
    }


    /**
     * @return Project
     */
    public function getDefaultConfiguration() : Project
    {
        $project = new Project(new BaseSlugifier());
        $project->setBasePort(8000);
        $project->setApplicationOptions($this->getApplicationConfig());
        $project->setPhpOptions($this->getPhpConfig());

        $project->setMysqlOptions($this->getMysqlConfig());
        $project->setMariadbOptions($this->getMariaDbConfig());
        $project->setPostgresOptions($this->getPostgresConfig());
        $project->setElasticsearchOptions($this->getElasticSearchConfig());
        $project->setMemcachedOptions($this->getMemcachedConfig());
        $project->setRedisOptions($this->getRedisConfig());
        $project->setMailhogOptions($this->getMailhogConfig());

        $this->checkPhpExtensions($project);

        return $project;
    }

    protected function checkPhpExtensions(Project $project)
    {
        $phpConfig = $project->getPhpOptions();
        $availExtensions = AvailableExtensionsFactory::create($phpConfig->getVersion());

        if ($project->getMemcachedOptions()->isEnabled() &&
            !$phpConfig->hasExtension('Memcached') &&
            $availExtensions->isAvailable('Memcached')
        ) {
            $phpConfig->addExtensionByName('Memcached');
        }

        if ($project->getRedisOptions()->isEnabled() &&
            !$phpConfig->hasExtension('Redis') &&
            $availExtensions->isAvailable('Redis')
        ) {
            $phpConfig->addExtensionByName('Redis');
        }

        if (($project->getMysqlOptions()->isEnabled() || $project->getMariadbOptions()->isEnabled()) &&
            !$phpConfig->hasExtension('MySQL') &&
            $availExtensions->isAvailable('MySQL')
        ) {
            $phpConfig->addExtensionByName('MySQL');
        }

        if ($project->getPostgresOptions()->isEnabled() &&
            !$phpConfig->hasExtension('PostgreSQL') &&
            $availExtensions->isAvailable('PostgreSQL')
        ) {
            $phpConfig->addExtensionByName('PostgreSQL');
        }
    }

    protected function getApplicationConfig() : Application
    {
        return $this->appConfigFactory->getDefaultConfiguration();
    }

    protected function getPhpConfig() : Php
    {
        return $this->phpConfigFactory->getDefaultConfiguration();
    }

    protected function getMysqlConfig() : MySQL
    {
        /** @var MySQL $mysql */
        $mysql = $this->mysqlConfigFactory->getDefaultConfiguration();
        return $mysql;
    }

    protected function getMariaDbConfig() : MariaDB
    {
        /** @var MariaDB $maria */
        $maria = $this->mariaDbConfigFactory->getDefaultConfiguration();
        return $maria;
    }

    protected function getPostgresConfig() : Postgres
    {
        return $this->postgresConfigFactory->getDefaultConfiguration();
    }

    protected function getElasticSearchConfig() : Elasticsearch
    {
        return $this->elasticSearchConfigFactory->getDefaultConfiguration();
    }

    protected function getMemcachedConfig() : Memcached
    {
        return $this->memcachedConfigFactory->getDefaultConfiguration();
    }

    protected function getRedisConfig() : Redis
    {
        return $this->redisConfigFactory->getDefaultConfiguration();
    }

    protected function getMailhogConfig() : Mailhog
    {
        return $this->mailHogConfigFactory->getDefaultConfiguration();
    }
}