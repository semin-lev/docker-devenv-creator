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
use Command\QuestionFactory\Traits\DbRootPasswordQuestionTrait;
use Command\QuestionFactory\Traits\DbUserNameQuestionTrait;
use Command\QuestionFactory\Traits\DbUserPasswordQuestionTrait;
use Command\QuestionFactory\Traits\IsEnabledQuestionTrait;
use Command\QuestionFactory\Traits\VersionQuestionTrait;
use PHPDocker\Project\ServiceOptions\AbstractMySQL;

abstract class AbstractMysqlQuestionFactory extends AbstractQuestionFactory
{
    use IsEnabledQuestionTrait,
        DbNameQuestionTrait,
        DbUserNameQuestionTrait,
        DbUserPasswordQuestionTrait,
        DbRootPasswordQuestionTrait,
        VersionQuestionTrait
    ;

    abstract function getOptions(): AbstractMySQL;

    protected function getDbUserName(): string
    {
        return $this->getOptions()->getUsername();
    }

    protected function isServiceEnabled(): bool
    {
        return $this->getOptions()->isEnabled();
    }

    protected function getDefaultPassword(): string
    {
        return '123';
    }

    protected function getDefaultDbName(): string
    {
        return 'db';
    }

    protected function getDefaultDbUserName(): string
    {
        return 'db_user';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultVersion(): string
    {
        return $this->getOptions()->getVersion();
    }


}