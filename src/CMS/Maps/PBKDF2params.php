<?php

/**
 * PBKDF2params
 *
 * PHP version 5
 *
 * @category  File
 * @package   ASN1
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

namespace FG\ASN1\Maps;

use FG\ASN1\Identifier;

/**
 * PBKDF2params
 *
 * from https://tools.ietf.org/html/rfc2898#appendix-A.3
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PBKDF2params
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            // technically, this is a CHOICE in RFC2898 but the other "choice" is, currently, more of a placeholder
            // in the RFC
            'salt'=> ['type' => Identifier::OCTET_STRING],
            'iterationCount'=> ['type' => Identifier::INTEGER],
            'keyLength' => [
                'type'     => Identifier::INTEGER,
                'optional' => true
            ],
            'prf' => AlgorithmIdentifier::MAP + ['optional' => true]
        ]
    ];
}
