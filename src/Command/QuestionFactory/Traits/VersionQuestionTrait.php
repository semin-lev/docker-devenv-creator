<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Command\QuestionFactory\Traits;


use Symfony\Component\Console\Question\ChoiceQuestion;

trait VersionQuestionTrait
{
    use BaseQuestionTrait;

    public function getVersionQuestion(): ChoiceQuestion
    {
        $defaultVersion = $this->getDefaultVersion();
        return new ChoiceQuestion(
            sprintf('Select %s version (%s)', $this->getServiceName(), $defaultVersion),
            $this->getListOfVersions(),
            $defaultVersion
        );
    }

    /**
     * @return string
     */
    abstract protected function getDefaultVersion(): string;

    /**
     * @return string[]
     */
    abstract protected function getListOfVersions(): array ;
}