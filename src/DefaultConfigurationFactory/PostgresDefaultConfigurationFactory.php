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


use PHPDocker\Project\ServiceOptions\Postgres;

class PostgresDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function getDefaultConfiguration() : Postgres
    {
        $config = new Postgres();
        $config->setEnabled(false);

        $config->setVersion(Postgres::VERSION_96);

        $config->setDatabaseName('db');
        $config->setRootUser('root');
        $config->setRootPassword('123');

        return $config;
    }
}