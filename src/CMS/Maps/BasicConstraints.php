<?php

/**
 * BasicConstraints
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
 * BasicConstraints
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class BasicConstraints
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'cA'                => [
                                             'type'     => Identifier::BOOLEAN,
                                             'optional' => true,
                                             'default'  => false
                                   ],
            'pathLenConstraint' => [
                                             'type' => Identifier::INTEGER,
                                             'optional' => true
                                   ]
        ]
    ];
}
