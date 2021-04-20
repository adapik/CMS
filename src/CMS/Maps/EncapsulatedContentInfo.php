<?php
/**
 * EncapsulatedContentInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class EncapsulatedContentInfo
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'contentType' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'eContent' => [
                'constant' => 0,
                'explicit' => true,
                'optional' => true,
                'type' => Identifier::OCTETSTRING
            ]
        ]
    ];
}
