<?php

/**
 * RSAPrivateKey
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

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

/**
 * RSAPrivateKey
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class RSAPrivateKey
{
    // version must be multi if otherPrimeInfos present
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'version' => [
                'type' => Identifier::INTEGER,
                'mapping' => ['two-prime', 'multi']
            ],
            'modulus' =>         ['type' => Identifier::INTEGER], // n
            'publicExponent' =>  ['type' => Identifier::INTEGER], // e
            'privateExponent' => ['type' => Identifier::INTEGER], // d
            'prime1' =>          ['type' => Identifier::INTEGER], // p
            'prime2' =>          ['type' => Identifier::INTEGER], // q
            'exponent1' =>       ['type' => Identifier::INTEGER], // d mod (p-1)
            'exponent2' =>       ['type' => Identifier::INTEGER], // d mod (q-1)
            'coefficient' =>     ['type' => Identifier::INTEGER], // (inverse of q) mod p
            'otherPrimeInfos' => OtherPrimeInfos::MAP + ['optional' => true]
        ]
    ];
}
