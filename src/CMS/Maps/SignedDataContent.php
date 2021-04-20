<?php

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 *
 */
class SignedDataContent
{
    const CERTIFICATES_TAG_NUMBER = 0;
    const CLR_TAG_NUMBER = 1;
    /**
     * SignedData ::= SEQUENCE {
     *      version CMSVersion,
     *      digestAlgorithms DigestAlgorithmIdentifiers,
     *      encapContentInfo EncapsulatedContentInfo,
     *      certificates [0] IMPLICIT CertificateSet OPTIONAL,
     *      crls [1] IMPLICIT RevocationInfoChoices OPTIONAL,
     *      signerInfos SignerInfos
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            // technically, default implies optional, but we'll define it as being optional, none-the-less, just to
            // reenforce that fact
            'version' => [
                'type' => Identifier::INTEGER,
                'mapping' => ['v1', 'v2', 'v3', 'v4', 'v5'],
                'default' => 'v1'
            ],
            'digestAlgorithms' => [
                'type' => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => AlgorithmIdentifier::MAP
            ],
            'encapsulatedContentInfo' => EncapsulatedContentInfo::MAP,
            'certificates' => [
                'implicit' => true,
                'optional' => true,
                'constant' => self::CERTIFICATES_TAG_NUMBER,
                'type' => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => Certificate::MAP
            ],
            'crl' => [
                'type' => Identifier::SEQUENCE,
                'explicit' => true,
                'optional' => true,
                'constant' => self::CLR_TAG_NUMBER,
                'min' => 0,
                'max' => -1,
                'children' => RevocationInfoChoices::MAP,
            ],
            'signerInfos' => [
                'type' => Identifier::SET,
                'min' => 0,
                'max' => -1,
                'children' => SignerInfo::MAP
            ],
        ]
    ];
}
