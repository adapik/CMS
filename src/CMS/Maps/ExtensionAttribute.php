<?php

/**
 * ExtensionAttribute
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
 * ExtensionAttribute
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class ExtensionAttribute
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
             'extension-attribute-type'  => [
                                                'type' => Identifier::PRINTABLE_STRING,
                                                'constant' => 0,
                                                'optional' => true,
                                                'implicit' => true
                                            ],
             'extension-attribute-value' => [
                                                'type' => Identifier::ANY,
                                                'constant' => 1,
                                                'optional' => true,
                                                'explicit' => true
                                            ]
        ]
    ];
}
