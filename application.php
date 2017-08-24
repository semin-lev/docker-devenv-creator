#!/usr/bin/env php
<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/vendor/autoload.php';

use Command\GenerateDevenvCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new GenerateDevenvCommand('generate'));

$application->run();