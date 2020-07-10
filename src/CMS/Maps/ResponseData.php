<?php
/**
 * ResponseData
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class ResponseData
{
    /**
     * ResponseData ::= SEQUENCE {
     *        version              [0] EXPLICIT Version DEFAULT v1,            # Integer
     *        responderID              ResponderID,                            # TaggedObject
     *        producedAt               GeneralizedTime,                        # GeneralizedTime
     *        responses                SEQUENCE OF SingleResponse,             # Sequence
     *        responseExtensions   [1] EXPLICIT Extensions OPTIONAL            # TaggedObject
     * }
     * ResponderID ::= CHOICE {
     *        byName   [1] Name,
     *        byKey    [2] KeyHash
     * }
     * KeyHash ::= OCTET STRING --SHA-1 hash of responder's public key
     *            --(excluding the tag and length fields)
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'version' => [
                'type' => Identifier::INTEGER,
                'represent' => 'INTEGER',
                'constant' => 0,
                'optional' => true,
                'explicit' => true,
                'mapping' => ['v1', 'v2', 'v3'],
                'default' => 'v1',
            ],
            'responderID' => [
                'type' => Identifier::ANY,
                'represent' => 'ANY',
                'children' => [
                    'byName' => [
                            'constant' => 1,
                            'represent' => 'SEQUENCE',
                        ] + RDNSequence::MAP,

                    'byKey' => ['constant' => 2, 'type' => Identifier::OCTETSTRING, 'represent' => 'OCTETSTRING',],
                ],
            ],
            'producedAt' => ['type' => Identifier::GENERALIZED_TIME],
            'responses' => [
                'type' => Identifier::SEQUENCE,
                'min' => 1,
                'max' => -1,
                'children' => SingleResponse::MAP,
            ],
            'responseExtensions' => [
                    'constant' => 1,
                    'optional' => true,
                    'explicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}