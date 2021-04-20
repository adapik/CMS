<?php

/**
 * PublicKey
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
 * PublicKey
 *
 * this format is not formally defined anywhere but is none-the-less the form you
 * get when you do "openssl rsa -in private.pem -outform PEM -pubout"
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PublicKey
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'publicKeyAlgorithm'=> AlgorithmIdentifier::MAP,
            'publicKey' => ['type' => Identifier::BITSTRING]
        ]
    ];
}
