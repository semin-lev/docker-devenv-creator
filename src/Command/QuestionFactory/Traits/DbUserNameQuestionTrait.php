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

trait DbUserNameQuestionTrait
{
    use BaseQuestionTrait;

    public function getDbUserNameQuestion()
    {
        $default = $this->getDefaultDbUserName();
        return new Question(
            sprintf("Application %s user name (default %s): ", $this->getServiceName(), $default),
            $default
        );
    }

    abstract protected function getDefaultDbUserName(): string;
}