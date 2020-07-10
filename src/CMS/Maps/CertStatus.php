<?php
/**
 * CertStatus
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class CertStatus
{
    /**
     * CertStatus ::= CHOICE {
     *        good                [0]     IMPLICIT NULL,
     *        revoked             [1]     IMPLICIT RevokedInfo,
     *        unknown             [2]     IMPLICIT UnknownInfo
     * }
     * RevokedInfo ::= SEQUENCE {
     *        revocationTime              GeneralizedTime,
     *        revocationReason    [0]     EXPLICIT CRLReason OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::CHOICE,
        'children' => [
            'good' => [
                'type' => Identifier::NULL,
                'constant' => 0,
                'implicit' => true,
            ],
            'revoked' => [
                'constant' => 1,
                'implicit' => true,
                'type' => Identifier::SEQUENCE,
                'children' => [
                    'revocationTime' => ['type' => Identifier::GENERALIZED_TIME],
                    'revocationReason' => [
                            'constant' => 0,
                            'explicit' => true,
                            'optional' => true,
                        ] + CRLReason::MAP,
                ],
            ],
            'unknown' => [
                'constant' => 2,
                'implicit' => true,
                'type' => Identifier::NULL,
            ],
        ],

    ];
}