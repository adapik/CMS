<?php
/**
 * TSTInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class TSTInfo
{
    /**
     * TSTInfo ::= SEQUENCE  {
     *      version                      INTEGER  { v1(1) },
     *      policy                       TSAPolicyId,
     *      messageImprint               MessageImprint,
     *      serialNumber                 INTEGER,
     *      genTime                      GeneralizedTime,
     *
     *      accuracy                     Accuracy                 OPTIONAL,
     *      ordering                     BOOLEAN             DEFAULT FALSE,
     *      nonce                        INTEGER                  OPTIONAL,
     *      -- MUST be present if the similar field was present
     *      -- in TimeStampReq.  In that case it MUST have the same value.
     *      tsa                          [0] GeneralName          OPTIONAL,
     *      extensions                   [1] IMPLICIT Extensions  OPTIONAL   }
     */

    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'version' => [
                'type' => Identifier::INTEGER,
                'mapping' => ['v1', 'v2', 'v3', 'v4', 'v5'],
                'default' => 'v1'
            ],
            'policy' => [
                'type' => Identifier::OBJECT_IDENTIFIER,
            ],
            'messageImprint' => MessageImprint::MAP,
            'serialNumber' => [
                'type' => Identifier::INTEGER
            ],
            'genTime' => [
                'type' => Identifier::GENERALIZED_TIME
            ],
            'accuracy' => [
                    'optional' => true
                ] + Accuracy::MAP,
            'ordering' => [
                'type' => Identifier::BOOLEAN,
                'default' => true,
                'optional' => true
            ],
            'nonce' => [
                'type' => Identifier::INTEGER,
                'optional' => true
            ],
            'tsa' => [
                    'optional' => true,
                    'constant' => 0
                ] + GeneralName::MAP,
            'extensions' => [
                    'constant' => 1,
                    'optional' => true,
                    'explicit' => true
                ] + Extensions::MAP
        ]
    ];
}