<?php

/**
 * NameConstraints
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
 * NameConstraints
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class NameConstraints
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'permittedSubtrees' => [
                                       'constant' => 0,
                                       'optional' => true,
                                       'implicit' => true
                                   ] + GeneralSubtrees::MAP,
            'excludedSubtrees'  => [
                                       'constant' => 1,
                                       'optional' => true,
                                       'implicit' => true
                                   ] + GeneralSubtrees::MAP
        ]
    ];
}
