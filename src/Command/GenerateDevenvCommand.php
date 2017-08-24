<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Command;


use PHPDocker\Generator\Factory;
use PHPDocker\Project\Project;
use Slugifier\BaseSlugifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDevenvCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('project_name', InputArgument::REQUIRED, 'Project name');
        $this->addArgument('extract_to_dir', InputArgument::OPTIONAL, 'Extract to dir', './');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = Factory::create();
        $project = new Project(new BaseSlugifier());

        $project->setName($input->getArgument('project_name'));

        $zip = $generator->generate($project);

        $arhiver = new \ZipArchive();

        try {
            $arhiver->open($zip->getTmpFilename());
            $arhiver->extractTo($input->getArgument('extract_to_dir'));
        } finally {
            $arhiver->close();
        }

        $output->writeln("End");

    }
}