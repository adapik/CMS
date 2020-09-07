<?php
/**
 * RevocationValuesAttribute
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 * It's a part of unsigned attributes
 * @see SignerInfo::MAP
 * @see Attributes::MAP
 */
abstract class RevocationValuesAttribute extends Attribute
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'type' => AttributeType::MAP,
            'attributeSet' => [
                'type' => Identifier::SET,
                'min' => 1,
                'max' => -1,
                'children' => RevocationValues::MAP
            ]
        ]
    ];
}
