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


use Symfony\Component\Console\Question\Question;

trait DbUserPasswordQuestionTrait
{
    use BaseQuestionTrait, BasePasswordTrait;

    public function getDbUserPasswordQuestion(): Question
    {
        $default = $this->getDefaultPassword();
        return new Question(
            sprintf("%s password (default %s): ", $this->getDbUserName(), $default),
            $default
        );
    }

    abstract protected function getDbUserName(): string;
}