<?php
/**
 * CMSInterface
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Interfaces;

use Adapik\CMS\CMSBase;

/**
 * Class CMSInterface
 * @package Adapik\CMS
 */
interface CMSInterface
{
    /**
     * @param string $content
     * @return mixed
     */
    public static function createFromContent(string $content): CMSInterface;
}
