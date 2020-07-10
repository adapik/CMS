<?php
/**
 * Request
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class Request
{
    /**
     * Request ::=     SEQUENCE {
     *        reqCert                    CertID,
     *        singleRequestExtensions    [0] EXPLICIT Extensions OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'reqCert' => CertID::MAP,
            'singleRequestExtensions' => [
                    'constant' => 0,
                    'optional' => true,
                    'explicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}