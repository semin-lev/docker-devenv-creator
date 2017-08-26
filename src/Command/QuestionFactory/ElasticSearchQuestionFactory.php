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


use Command\QuestionFactory\Traits\IsEnabledQuestionTrait;
use Command\QuestionFactory\Traits\VersionQuestionTrait;
use PHPDocker\Project\ServiceOptions\Elasticsearch;

class ElasticSearchQuestionFactory extends AbstractQuestionFactory
{
    use IsEnabledQuestionTrait, VersionQuestionTrait;

    /**
     * Must return name of service
     *
     * @return string
     */
    protected function getServiceName(): string
    {
        return 'ElasticSearch';
    }

    protected function isServiceEnabled(): bool
    {
        return $this->getProject()->getElasticsearchOptions()->isEnabled();
    }

    /**
     * @return string
     */
    protected function getDefaultVersion(): string
    {
        return $this->getProject()->getElasticsearchOptions()->getVersion();
    }

    /**
     * @return string[]
     */
    protected function getListOfVersions(): array
    {
        return Elasticsearch::getChoices();
    }
}