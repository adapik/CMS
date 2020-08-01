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

use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Sequence;

/**
 * Class GeneralName
 *
 * @see     Maps\GeneralName
 * @package Adapik\CMS
 */
class GeneralName extends CMSBase
{
    /**
     * @var ExplicitlyTaggedObject
     */
    protected $object;

    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\GeneralName::class, Sequence::class));
    }
}