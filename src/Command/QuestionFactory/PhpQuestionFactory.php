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


use Command\QuestionFactory\Traits\VersionQuestionTrait;
use PHPDocker\PhpExtension\AvailableExtensionsFactory;
use PHPDocker\Project\ServiceOptions\Php;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PhpQuestionFactory extends AbstractQuestionFactory
{
    use VersionQuestionTrait;

    public function getConfirmDisableExtensionQuestion(): ConfirmationQuestion
    {
        $project = $this->getProject();
        return new ConfirmationQuestion(
            sprintf(
                'Do you want to disable some default extensions? They are %s. Y/n (n): ',
                implode(", ", $project->getPhpOptions()->getExtensionNames())
            ),
            false
        );
    }

    public function getForDisableQuestion(): ChoiceQuestion
    {
        $project = $this->getProject();
        $forDisableQuestion = new ChoiceQuestion(
            'Pls, choose extensions for disable',
            $project->getPhpOptions()->getExtensionNames()
        );
        $forDisableQuestion->setMultiselect(true);

        return $forDisableQuestion;
    }

    public function getConfirmEnableExtensionQuestion(): ConfirmationQuestion
    {
        return new ConfirmationQuestion(
            sprintf(
                'Do you want to enable some additional extensions? %s are available. Y/n (n):',
                implode(", ", $this->getAvailableForEnabledExtension())
            ),
            false
        );
    }

    public function getForEnableQuestion(): ChoiceQuestion
    {
        $extensionsQuestion = new ChoiceQuestion(
            'Pls, choose extensions for enable',
            $this->getAvailableForEnabledExtension()
        );
        $extensionsQuestion->setMultiselect(true);

        return $extensionsQuestion;
    }

    protected function getAvailableForEnabledExtension(): array
    {
        $project = $this->getProject();
        $extension = AvailableExtensionsFactory::create($project->getPhpOptions()->getVersion());
        return array_diff(array_keys($extension->getAll()), $project->getPhpOptions()->getExtensionNames());
    }

    /**
     * Must return name of service
     *
     * @return string
     */
    protected function getServiceName(): string
    {
        return 'php';
    }

    /**
     * @return string
     */
    protected function getDefaultVersion(): string
    {
        return $this->getProject()->getPhpOptions()->getVersion();
    }

    /**
     * @return string[]
     */
    protected function getListOfVersions(): array
    {
        return Php::getSupportedVersions();
    }
}