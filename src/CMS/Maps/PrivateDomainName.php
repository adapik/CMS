<?php

/**
 * PrivateDomainName
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
 * PrivateDomainName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PrivateDomainName
{
    const MAP = [
        'type'     => Identifier::CHOICE,
        'children' => [
            'numeric'   => ['type' => Identifier::NUMERIC_STRING],
            'printable' => ['type' => Identifier::PRINTABLE_STRING]
        ]
    ];
}
