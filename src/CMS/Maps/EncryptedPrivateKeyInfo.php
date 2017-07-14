<?php

/**
 * EncryptedPrivateKeyInfo
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
 * EncryptedPrivateKeyInfo
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class EncryptedPrivateKeyInfo
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'encryptionAlgorithm' => AlgorithmIdentifier::MAP,
            'encryptedData'       => EncryptedData::MAP
        ]
    ];
}
