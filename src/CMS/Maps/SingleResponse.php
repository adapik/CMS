<?php
/**
 * SingleResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class SingleResponse
{
    /**
     * SingleResponse ::= SEQUENCE {
     *        certID                       CertID,                                 # Sequence
     *        certStatus                   CertStatus,                             # TaggedObject
     *        thisUpdate                   GeneralizedTime,                        # GeneralizedTime
     *        nextUpdate           [0]     EXPLICIT GeneralizedTime OPTIONAL,      # TaggedObject
     *        singleExtensions     [1]     EXPLICIT Extensions OPTIONAL            # TaggedObject
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'certID' => CertID::MAP,
            'certStatus' => CertStatus::MAP,
            'thisUpdate' => [
                'type' => Identifier::GENERALIZED_TIME,
            ],
            'nextUpdate' => [
                'constant' => 0,
                'optional' => true,
                'explicit' => true,
                'type' => Identifier::GENERALIZED_TIME,
            ],
            'singleExtensions' => [
                    'constant' => 1,
                    'optional' => true,
                    'explicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}