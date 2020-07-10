<?php
/**
 * RevocationValues
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class RevocationValues
{
    /**
     * RevocationValues ::=  SEQUENCE {
     *      certificateList         [0] SEQUENCE OF CertificateList OPTIONAL,
     *      basicOCSPResponses      [1] SEQUENCE OF BasicOCSPResponse OPTIONAL,
     *      otherRevValues          [2] OtherRevVals OPTIONAL
     * }
     *
     * OtherRevVals ::= SEQUENCE {
     *      OtherRevValType   OtherRevValType,
     *      OtherRevVals      ANY DEFINED BY OtherRevValType
     * }
     *
     * OtherRevValType ::= OBJECT IDENTIFIER
     */

    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'certificateList' => [
                'type' => Identifier::SEQUENCE,
                'constant' => 0,
                'explicit' => true,
                'optional' => true,
                'min' => 1,
                'max' => -1,
                'children' => CertificateList::MAP
            ],
            'basicOCSPResponses' => [
                'type' => Identifier::SEQUENCE,
                'constant' => 1,
                'explicit' => true,
                'optional' => true,
                'min' => 1,
                'max' => -1,
                'children' => BasicOCSPResponse::MAP
            ],
            'otherRevValues' => [
                'type' => Identifier::SEQUENCE,
                'constant' => 1,
                'optional' => true,
                'children' => [
                    'OtherRevValType' => ['type' => Identifier::OBJECT_IDENTIFIER],
                    'OtherRevVals' => ['type' => Identifier::ANY],
                ]
            ]
        ]
    ];
}
