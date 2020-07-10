<?php

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 * SignerInfo Map
 */
class SignerInfo
{
    /**
     * SignerInfo ::= SEQUENCE {
     *      version                 CMSVersion,
     *      sid                     SignerIdentifier,
     *      digestAlgorithm         DigestAlgorithmIdentifier,
     *      signedAttrs         [0] IMPLICIT SignedAttributes OPTIONAL,
     *      signatureAlgorithm      SignatureAlgorithmIdentifier,
     *      signature               SignatureValue,
     *      unsignedAttrs [1]       IMPLICIT UnsignedAttributes OPTIONAL
     * }
     */
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
                    'constant' => 0,
                    'optional' => true,
                    'implicit' => true
                ] + Attributes::MAP,
            'signatureAlgorithm' => AlgorithmIdentifier::MAP,
            'signature' => ['type' => Identifier::OCTETSTRING],
            'unsignedAttrs' => [
                    'constant' => 1,
                    'optional' => true,
                    'implicit' => true
                ] + Attributes::MAP
            ,
        ]
    ];
}
