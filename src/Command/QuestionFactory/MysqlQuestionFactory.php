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


use PHPDocker\Project\ServiceOptions\AbstractMySQL;
use PHPDocker\Project\ServiceOptions\MySQL;

class MysqlQuestionFactory extends AbstractMysqlQuestionFactory
{
    /**
     * Must return name of service
     *
     * @return string
     */
    protected function getServiceName(): string
    {
        return 'MySQL';
    }

    function getOptions(): AbstractMySQL
    {
        return $this->getProject()->getMysqlOptions();
    }

    /**
     * @return string[]
     */
    protected function getListOfVersions(): array
    {
        return MySQL::getChoices();
    }
}