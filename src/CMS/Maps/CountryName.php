<?php

/**
 * CountryName
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
 * CountryName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class CountryName
{
    const MAP = [
        'type'     => Identifier::CHOICE,
        // if class isn't present it's assumed to be \FG\ASN1::CLASS_UNIVERSAL or
        // (if constant is present) \FG\ASN1::CLASS_CONTEXT_SPECIFIC
        'class'    => 1, //ASN1::CLASS_APPLICATION,
        'cast'     => 1,
        'children' => [
            'x121-dcc-code'        => ['type' => Identifier::NUMERIC_STRING],
            'iso-3166-alpha2-code' => ['type' => Identifier::PRINTABLE_STRING]
        ]
    ];
}
