<?php
/**
 * Accuracy
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class Accuracy
{
    /**
     * Accuracy ::= SEQUENCE {
     *      seconds        INTEGER           OPTIONAL,
     *      millis     [0] INTEGER  (1..999) OPTIONAL,
     *      micros     [1] INTEGER  (1..999) OPTIONAL
     * }
     *
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'seconds' => [
                'type' => Identifier::INTEGER,
                'optional' => true
            ],
            'millis' => [
                'type' => Identifier::INTEGER,
                'constant' => 0,
                'optional' => true
            ],
            'micros' => [
                'type' => Identifier::INTEGER,
                'constant' => 1,
                'optional' => true
            ],
        ]
    ];
}
