<?php

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 *
 */
class SignedDataContent
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            // technically, default implies optional, but we'll define it as being optional, none-the-less, just to
            // reenforce that fact
            'version'             => [
                'type'    => Identifier::INTEGER,
                'mapping' => ['v1', 'v2', 'v3', 'v4', 'v5'],
                'default'  => 'v1'
            ],
            'digestAlgorithms' => [
                'type'    => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => AlgorithmIdentifier::MAP
            ],
            'encapsulatedContentInfo' => [
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
            ],
            'certificates' => [
                'implicit' => true,
                'optional' => true,
                'constant' => 0,
                'type' => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => Certificate::MAP
            ],
            'crl' => [
                'explicit' => true,
                'optional' => true,
                'constant' => 0,
                'type' => Identifier::SEQUENCE,
                'min' => 0,
                'max' => -1,
                'children' => CertificateList::MAP
            ],
            'signerInfos' => [
                'type'    => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => SignerInfo::MAP
            ],
        ]
    ];
}
