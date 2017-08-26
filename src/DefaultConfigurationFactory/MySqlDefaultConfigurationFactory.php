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


use PHPDocker\Project\ServiceOptions\AbstractMySQL;
use PHPDocker\Project\ServiceOptions\MariaDB;
use PHPDocker\Project\ServiceOptions\MySQL;

class MySqlDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    const MYSQL = 'mysql';
    const MARIADB = 'mariadb';

    const CLASS_MAP = [
        self::MYSQL   => MySQL::class,
        self::MARIADB => MariaDB::class,
    ];

    const DEFAULT_VERSION_MAP = [
        self::MYSQL   => MySQL::VERSION_57,
        self::MARIADB => MariaDB::VERSION_101
    ];

    private $type;

    /**
     * MySqlDefaultConfigurationFactory constructor.
     * @param string $type mysql or mariadb
     */
    public function __construct($type = self::MYSQL)
    {
        $this->type = $type;
    }

    /**
     * @return AbstractMySQL
     */
    public function getDefaultConfiguration() : AbstractMySQL
    {
        $config = $this->createClass();
        $config->setEnabled($this->type == self::MYSQL);
        $config->setVersion($this->getDefaultVersion());
        $config->setRootPassword('123');
        $config->setUsername('db_user');
        $config->setPassword('123');
        $config->setDatabaseName('db');

        return $config;
    }

    protected function getDefaultVersion()
    {
        return self::DEFAULT_VERSION_MAP[$this->type];
    }

    /**
     * @return AbstractMySQL
     */
    protected function createClass() : AbstractMySQL
    {
        $className = $this->getClassName();
        $reflection = new \ReflectionClass($className);

        if ($reflection->getConstructor()->getNumberOfRequiredParameters() > 0) {
            throw new \RuntimeException(sprintf(
                "Unable to create object of class %s, there are required parameters in constructor",
                $className
            ));
        }

        $object = $reflection->newInstance();
        if (!$object instanceof AbstractMySQL) {
            throw new \RuntimeException(sprintf("Class %s must be instance of %s", $className, AbstractMySQL::class));
        }

        return $object;
    }

    protected function getClassName() : string
    {
        $className = self::CLASS_MAP[$this->type];
        if (!$className) {
            throw new \RuntimeException(sprintf("Unable to find class for %s type", $this->type));
        }

        return $className;
    }
}