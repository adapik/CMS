<?php

/**
 * PKCS9String 
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
 * PKCS9String 
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PKCS9String 
{
    const MAP = [
        'type'     => Identifier::CHOICE,
        'children' => [
            'ia5String'       => ['type' => Identifier::IA5_STRING],
            'directoryString' => DirectoryString::MAP
        ]
    ];
}
