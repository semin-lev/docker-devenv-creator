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


use PHPDocker\Project\ServiceOptions\Mailhog;

class MailhogDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    /**
     * @return Mailhog
     */
    public function getDefaultConfiguration() : Mailhog
    {
        $config = new Mailhog();
        $config->setEnabled(false);

        return $config;
    }
}