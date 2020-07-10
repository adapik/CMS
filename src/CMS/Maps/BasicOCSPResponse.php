<?php
/**
 * BasicOCSPResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class BasicOCSPResponse
{
    /**
     * BasicOCSPResponse       ::= SEQUENCE {
     *        tbsResponseData      ResponseData,                                       # Sequence
     *        signatureAlgorithm   AlgorithmIdentifier,                                # Sequence
     *        signature            BIT STRING,                                         # BIT STRING
     *        certs                [0] EXPLICIT SEQUENCE OF Certificate OPTIONAL       # [0]
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'tbsResponseData' => ResponseData::MAP,
            'signatureAlgorithm' => AlgorithmIdentifier::MAP,
            'signature' => ['type' => Identifier::BITSTRING],
            'certs' => [
                'constant' => 0,
                'explicit' => true,
                'type' => Identifier::SEQUENCE,
                'children' => Certificate::MAP,
                'optional' => true,
                'min' => 1,
                'max' => -1,
            ],
        ],
    ];
}