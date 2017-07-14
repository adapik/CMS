<?php

/**
 * AlgorithmIdentifier
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
 * AlgorithmIdentifier
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class AlgorithmIdentifier
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'algorithm'  => ['type' => Identifier::OBJECT_IDENTIFIER],
            'parameters' => [
                'type'     => Identifier::ANY,
                'optional' => true
             ]
        ]
    ];
}
