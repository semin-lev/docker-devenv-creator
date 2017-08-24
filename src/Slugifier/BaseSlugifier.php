<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slugifier;


use PHPDocker\Interfaces\SlugifierInterface;

class BaseSlugifier implements SlugifierInterface
{

    /**
     * Takes a string and returns a slugified version of it.
     *
     * @param string $string
     *
     * @return string
     */
    public function slugify(string $string): string
    {
        return $string;
    }
}