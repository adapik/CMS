<?php

namespace CMS\Maps;

use Adapik\CMS\Maps\AlgorithmIdentifier;
use Adapik\CMS\Maps\Certificate;
use Adapik\CMS\Maps\CertificateList;
use FG\ASN1\Identifier;

/**
 *
 */
class SignedData
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            // technically, default implies optional, but we'll define it as being optional, none-the-less, just to
            // reenforce that fact
            'version'             => [
                'type'    => Identifier::INTEGER,
                'constant' => 0,
                'explicit' => true,
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
                    'contentType' => Identifier::OBJECT_IDENTIFIER,
                    'eContent' => [
                        'constant' => 0,
                        'explicit' => true,
                        'optional' => true,
                        'type' => Identifier::OCTETSTRING
                    ]
                ]
            ],
            'certificates' => [
                'explicit' => true,
                'constant' => 0,
                'type' => Identifier::SEQUENCE,
                'min' => 0,
                'max' => -1,
                'children' => Certificate::MAP
            ],
            'crl' => [
                'explicit' => true,
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
