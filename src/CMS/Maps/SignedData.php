<?php

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 *
 */
class SignedData
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'contentType' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'content' => [
                'constant' => 0,
                'explicit' => true
                ] + SignedDataContent::MAP
        ]
    ];
}
