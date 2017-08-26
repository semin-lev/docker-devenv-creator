<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DefaultConfigurationFactory;


use PHPDocker\Project\ServiceOptions\Application;
use PHPDocker\Project\ServiceOptions\Base;

class ApplicationDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    /**
     * @return Application
     */
    public function getDefaultConfiguration() : Application
    {
        $application = new Application();
        $application->setApplicationType(Application::APPLICATION_TYPE_SYMFONY);

        return $application;
    }
}