<?php
/**
 * UnsignedAttribute
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

class UnsignedAttribute
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => Attributes::MAP,
    ];
}
