<?php

/**
 * PublicKeyAndChallenge
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
 * PublicKeyAndChallenge
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PublicKeyAndChallenge
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'spki'      => SubjectPublicKeyInfo::MAP,
            'challenge' => ['type' => Identifier::IA5_STRING]
        ]
    ];
}
