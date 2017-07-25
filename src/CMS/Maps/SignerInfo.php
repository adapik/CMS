<?php

namespace CMS\Maps;

use Adapik\CMS\Maps\AlgorithmIdentifier;
use Adapik\CMS\Maps\Attributes;
use Adapik\CMS\Maps\CertificateSerialNumber;
use Adapik\CMS\Maps\KeyIdentifier;
use Adapik\CMS\Maps\Name;
use FG\ASN1\Identifier;

/**
 * SignerInfo Map
 */
class SignerInfo
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'version'             => [
                'type'    => Identifier::INTEGER,
                'mapping' => ['v1', 'v2', 'v3', 'v4', 'v5'],
            ],
            'signerIdentifier' => [
                'type' => Identifier::CHOICE,
                'children' => [
                    'issuerAndSerialNumber' => [
                        'type' => Identifier::SEQUENCE,
                        'children' => [
                            'issuer' => Name::MAP,
                            'serialNumber' => CertificateSerialNumber::MAP
                        ]
                    ],
                    'subjectKeyIdentifier' => [
                            'constant' => 0,
                            'implicit' => true
                        ] + KeyIdentifier::MAP,
                ]
            ],
            'digestAlgorithm' => AlgorithmIdentifier::MAP,
            'signedAttrs' => [
                [
                    'constant' => 0,
                    'optional' => true,
                    'implicit' => true
                ] + Attributes::MAP,
            ],
            'signatureAlgorithm' => AlgorithmIdentifier::MAP,
            'signature' => ['type' => Identifier::OCTETSTRING],
            'unsignedAttrs' => [
                [
                    'constant' => 0,
                    'optional' => true,
                    'implicit' => true
                ] + Attributes::MAP,
            ],
        ]
    ];
}
