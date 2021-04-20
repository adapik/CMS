<?php
/**
 * GeneralName
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\ExplicitlyTaggedObject;

/**
 * Class GeneralName
 *
 * @see     Maps\GeneralName
 * @package Adapik\CMS
 */
class GeneralName extends CMSBase
{
    /**
     * @param string $content
     * @return GeneralName
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\GeneralName::class, ExplicitlyTaggedObject::class));
    }
}
