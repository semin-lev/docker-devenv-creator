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


use PHPDocker\Project\Project;
use PHPDocker\Project\ServiceOptions\Application;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class ApplicationQuestionFactory extends AbstractQuestionFactory
{
    public function getAppTypeQuestion(): ChoiceQuestion
    {
        $project = $this->getProject();
        return new ChoiceQuestion(
            'Choice application type. The default is '.$project->getApplicationOptions()->getApplicationType(),
            Application::getChoices(),
            $project->getApplicationOptions()->getApplicationType()
        );
    }

    public function getPortQuestion(): Question
    {
        $project = $this->getProject();
        $portQuest = new Question(
            sprintf("Select external port, default is %d: ", $project->getBasePort()),
            $project->getBasePort()
        );
        $portQuest->setNormalizer(function ($value){
            return (int)$value;
        });
        $portQuest->setValidator(function ($port){
            if ($port<=1000) {
                throw new \RuntimeException(sprintf("The port must be more than 1000"));
            }
            if ($port>9999) {
                throw new \RuntimeException(sprintf("The port must be lest than 9999"));
            }
            return $port;
        });

        return $portQuest;
    }

    public function getPathToSSHKeysQuestion(): Question
    {
        $quest = new Question(
            sprintf("What is the path to your ssh keys, default is %s", $this->getProject()->getPathToSSHKeys()),
            $this->getProject()->getPathToSSHKeys()
        );

        /*$quest->setValidator(function ($path){
            if (!is_dir($path)) {
                throw new \RuntimeException(sprintf("Path %s doesn't exist", $path));
            }
            return $path;
        });*/

        return $quest;
    }
}