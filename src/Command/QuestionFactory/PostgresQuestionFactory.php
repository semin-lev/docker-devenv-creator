<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Command\QuestionFactory;


use Command\QuestionFactory\Traits\DbNameQuestionTrait;
use Command\QuestionFactory\Traits\DbUserNameQuestionTrait;
use Command\QuestionFactory\Traits\DbUserPasswordQuestionTrait;
use Command\QuestionFactory\Traits\IsEnabledQuestionTrait;
use Command\QuestionFactory\Traits\VersionQuestionTrait;
use PHPDocker\Project\ServiceOptions\Postgres;

class PostgresQuestionFactory extends AbstractQuestionFactory
{
    use IsEnabledQuestionTrait,
        VersionQuestionTrait,
        DbUserNameQuestionTrait,
        DbUserPasswordQuestionTrait,
        DbNameQuestionTrait;

    protected function getDefaultPassword(): string
    {
        return '123';
    }

    /**
     * @inheritDoc
     */
    protected function getServiceName(): string
    {
        return 'Postgres';
    }

    protected function getDefaultDbName(): string
    {
        return 'db';
    }

    protected function getDefaultDbUserName(): string
    {
        return 'db_user';
    }

    protected function getDbUserName(): string
    {
        return $this->getProject()->getPostgresOptions()->getRootUser();
    }

    protected function isServiceEnabled(): bool
    {
        return $this->getProject()->getPostgresOptions()->isEnabled();
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultVersion(): string
    {
        return $this->getProject()->getPostgresOptions()->getVersion();
    }

    /**
     * @inheritDoc
     */
    protected function getListOfVersions(): array
    {
        return Postgres::getChoices();
    }


}