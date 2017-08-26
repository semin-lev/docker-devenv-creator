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


use Symfony\Component\Console\Question\ConfirmationQuestion;

trait IsEnabledQuestionTrait
{
    use BaseQuestionTrait;

    public function getIsEnabledQuestion() : ConfirmationQuestion
    {
        $default = $this->isServiceEnabled();
        return new ConfirmationQuestion(
            sprintf('Do you want to enable %s? Y/n (%s): ', $this->getServiceName(), $default ? 'Y' : 'n'),
            $default
        );
    }

    abstract protected function isServiceEnabled(): bool;
}