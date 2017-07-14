<?php

/**
 * AnotherName
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
 * AnotherName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class AnotherName
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
             'type-id' => ['type' => Identifier::OBJECT_IDENTIFIER],
             'value'   => [
                              'type' => Identifier::ANY,
                              'constant' => 0,
                              'optional' => true,
                              'explicit' => true
                          ]
        ]
    ];
}
