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

class MailhogQuestionFactory extends AbstractQuestionFactory
{
    use IsEnabledQuestionTrait;

    /**
     * @inheritDoc
     */
    protected function getServiceName(): string
    {
        return 'Mailhog';
    }

    protected function isServiceEnabled(): bool
    {
        return $this->getProject()->getMailhogOptions()->isEnabled();
    }
}