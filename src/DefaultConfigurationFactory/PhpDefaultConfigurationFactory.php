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


use PHPDocker\PhpExtension\AvailableExtensionsFactory;
use PHPDocker\Project\ServiceOptions\Php;

class PhpDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    /**
     * @return Php
     */
    public function getDefaultConfiguration() : Php
    {
        $php = new Php();
        $php->setVersion(Php::PHP_VERSION_72);

        $extensions = AvailableExtensionsFactory::create($php->getVersion());
        $neededExtension = [
            'cURL',
            'JSON',
            'MCrypt',
            'OPCache',
            'XML',
            'Zip',
            'Memcached',
            'GD',
            'Xdebug',
            'Intl',
            'MBSTRING'
        ];

        $installedExtension = array_intersect($neededExtension, array_keys($extensions->getAll()));
        foreach ($installedExtension as $extName) {
            $php->addExtensionByName($extName);
        }

        return $php;
    }
}