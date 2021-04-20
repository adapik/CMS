<?php

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class IssuerAndSerialNumber
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'issuer' => Name::MAP,
            'serialNumber' => CertificateSerialNumber::MAP,
        ],
    ];
}
