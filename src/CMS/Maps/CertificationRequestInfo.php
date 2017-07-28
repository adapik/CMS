<?php

/**
 * CertificationRequestInfo
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
 * CertificationRequestInfo
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class CertificationRequestInfo
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'version'       => [
                                   'type' => Identifier::INTEGER,
                                   'mapping' => ['v1']
                               ],
            'subject'       => Name::MAP,
            'subjectPKInfo' => SubjectPublicKeyInfo::MAP,
            'attributes'    => [
                                   'constant' => 0,
                                   'optional' => true,
                                   'implicit' => true
                               ] + Attributes::MAP,
        ]
    ];
}
