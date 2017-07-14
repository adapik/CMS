<?php

/**
 * BuiltInDomainDefinedAttribute
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
 * BuiltInDomainDefinedAttribute
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class BuiltInDomainDefinedAttribute
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
             'type'  => ['type' => Identifier::PRINTABLE_STRING],
             'value' => ['type' => Identifier::PRINTABLE_STRING]
        ]
    ];
}
