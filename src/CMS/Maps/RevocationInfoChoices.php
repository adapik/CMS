<?php
/**
 * RevocationInfoChoice
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

class RevocationInfoChoices
{
    /**
     * RevocationInfoChoice ::= CHOICE {
     *      crl        CertificateList,
     *      other  [1] IMPLICIT OtherRevocationInfoFormat }
     *
     * OtherRevocationInfoFormat ::= SEQUENCE {
     *      otherRevInfoFormat  OBJECT IDENTIFIER,
     *      otherRevInfo        ANY DEFINED BY otherRevInfoFormat
     * }
     */
    const MAP = [
        'type' => Identifier::CHOICE,
        'children' => [
            'crl' => [
                    'min' => 0,
                    'max' => 1,
                ] + CertificateList::MAP,
            'other' => [
                'constant' => 1,
                'implicit' => true,
                'min' => 0,
                'max' => 1,
                'children' => [
                    'type' => Identifier::SEQUENCE,
                    'children' => [
                        'otherRevInfoFormat' => ['type' => Identifier::OBJECT_IDENTIFIER],
                        'otherRevInfo' => ['type' => Identifier::ANY],
                    ]
                ]
            ]
        ]
    ];
}
