<?php

/**
 * DisplayText
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
 * DisplayText
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class DisplayText
{
    const MAP = [
        'type'     => Identifier::CHOICE,
        'children' => [
            'ia5String'     => ['type' => Identifier::IA5_STRING],
            'visibleString' => ['type' => Identifier::VISIBLE_STRING],
            'bmpString'     => ['type' => Identifier::BMP_STRING],
            'utf8String'    => ['type' => Identifier::UTF8_STRING]
        ]
    ];
}
