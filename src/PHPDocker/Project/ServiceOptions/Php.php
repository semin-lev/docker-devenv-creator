<?php
/**
 * Copyright 2016 Luis Alberto Pabon Flores
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PHPDocker\Project\ServiceOptions;

use PHPDocker\PhpExtension\AvailableExtensionsFactory;
use PHPDocker\PhpExtension\PhpExtension;

/**
 * Options for PHP container.
 *
 * @package PHPDocker\Project\ServiceOptions
 * @author  Luis A. Pabon Flores
 */
class Php extends Base
{
    /**
     * PHP 7.0.x
     */
    const PHP_VERSION_70 = '7.0.x';

    /**
     * PHP 7.1.x
     */
    const PHP_VERSION_71 = '7.1.x';

    /**
     * PHP 7.2.x
     */
    const PHP_VERSION_72 = '7.2.x';

    /**
     * PHP 5.6.x
     */
    const PHP_VERSION_56 = '5.6.x';

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * Supported PHP versions
     */
    const SUPPORTED_VERSIONS = [
        self::PHP_VERSION_72,
        self::PHP_VERSION_71,
        self::PHP_VERSION_70,
        self::PHP_VERSION_56,
    ];

    /**
     * @var string
     */
    protected $version;

    public function __construct()
    {
        $this->setEnabled(true);
    }

    /**
     * @return array|PhpExtension[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param array $phpExtensions
     *
     * @return Php
     */
    public function setPhpExtensions(array $phpExtensions): self
    {
        foreach ($phpExtensions as $phpExtension) {
            $this->addExtensionByName($phpExtension);
        }

        return $this;
    }

    /**
     * Adds an extension given the name only.
     *
     * @param string $extensionName
     *
     * @return Php
     */
    public function addExtensionByName(string $extensionName): self
    {
        $extensionInstance = AvailableExtensionsFactory::create($this->getVersion());

        $this->addExtension($extensionInstance->getPhpExtension($extensionName));

        return $this;
    }

    /**
     * @param PhpExtension $extension
     *
     * @return Php
     */
    public function addExtension(PhpExtension $extension): self
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @param array $extNames
     * @return Php
     */
    public function addExtensionsByName(array $extNames): self
    {
        foreach ($extNames as $val) {
            $this->addExtensionByName($val);
        }

        return $this;
    }

    /**
     * @param string $extensionName
     * @return bool
     */
    public function hasExtension(string $extensionName): bool
    {
        foreach ($this->getExtensions() as $extension) {
            if ($extension->getName() == $extensionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getExtensionNames() : array
    {
        $result = [];
        foreach ($this->getExtensions() as $ext) {
            $result[] = $ext->getName();
        }

        return $result;
    }

    /**
     * @param string $extName
     * @return Php
     */
    public function removeExtension(string $extName): self
    {
        $keyForUnset = null;
        foreach ($this->getExtensions() as $key => $ext) {
            if ($ext->getName() == $extName) {
                $keyForUnset = $key;
                break;
            }
        }

        if ($keyForUnset!==null) {
            unset($this->extensions[$keyForUnset]);
        }

        return $this;
    }

    /**
     * @param array|string[] $extensionNames
     * @return Php
     */
    public function removeExtensions(array $extensionNames): self
    {
        foreach ($extensionNames as $extName)
        {
            $this->removeExtension($extName);
        }

        return $this;
    }

    /**
     * @return Php
     */
    public function clearAllExtensions(): self
    {
        $this->extensions = [];
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return Php
     */
    public function setVersion(string $version): self
    {
        if (in_array($version, self::SUPPORTED_VERSIONS, true) === false) {
            throw new \InvalidArgumentException(sprintf('PHP version specified (%s) is unsupported', $version));
        }
        
        $this->version = $version;

        // update extensions
        $extNames = $this->getExtensionNames();
        $this->clearAllExtensions();
        
        $extensionsFactory = AvailableExtensionsFactory::create($this->version);
        foreach ($extNames as $extName) {
            if ($extensionsFactory->isAvailable($extName)) {
                $this->addExtensionByName($extName);
            }
        }

        return $this;
    }

    /**
     * Returns an array of supported PHP versions.
     *
     * @return array
     */
    public static function getSupportedVersions(): array
    {
        return self::SUPPORTED_VERSIONS;
    }
}
