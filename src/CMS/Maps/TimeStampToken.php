<?php
/**
 * TimeStampToken
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class TimeStampToken extends Attribute
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'contentType' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'content' => [
                'type' => Identifier::SET,
                'constant' => 0,
                'min' => 1,
                'max' => -1,
                'children' => SignedData::MAP
            ]
        ]
    ];
}
