<?php

/**
 * PersonalName
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
 * PersonalName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PersonalName
{
    const MAP = [
        'type'     => Identifier::SET,
        'children' => [
            'surname'              => [
                                       'type' => Identifier::PRINTABLE_STRING,
                                       'constant' => 0,
                                       'optional' => true,
                                       'implicit' => true
                                     ],
            'given-name'           => [
                                       'type' => Identifier::PRINTABLE_STRING,
                                       'constant' => 1,
                                       'optional' => true,
                                       'implicit' => true
                                     ],
            'initials'             => [
                                       'type' => Identifier::PRINTABLE_STRING,
                                       'constant' => 2,
                                       'optional' => true,
                                       'implicit' => true
                                     ],
            'generation-qualifier' => [
                                       'type' => Identifier::PRINTABLE_STRING,
                                       'constant' => 3,
                                       'optional' => true,
                                       'implicit' => true
                                     ]
        ]
    ];
}
