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


use PHPDocker\Project\ServiceOptions\Elasticsearch;

class ElasticSearchDefaultConfigurationFactory implements DefaultConfigurationFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function getDefaultConfiguration() : Elasticsearch
    {
        $config = new Elasticsearch();
        $config->setEnabled(false);
        $config->setVersion(Elasticsearch::VERSION_54);

        return $config;
    }

}