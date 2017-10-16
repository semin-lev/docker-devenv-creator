<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPDocker\Generator\GeneratedFile;


class BinaryExecutorFile extends Base
{
    /** @var  string */
    private $fileName;

    /**
     * @inheritDoc
     */
    public function __construct($fileName, $contents)
    {
        $this->fileName = $fileName;
        parent::__construct($contents);
    }

    /**
     * Must return the relative filename this file will be described by.
     *
     * Eg:
     *   - Folder\SomeFile
     *
     * @return string
     */
    public function getFilename(): string
    {
        return 'bin'.DIRECTORY_SEPARATOR.$this->fileName;
    }
}