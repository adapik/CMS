<?php

/**
 * PolicyInformation
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
 * PolicyInformation
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PolicyInformation
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'policyIdentifier' => CertPolicyId::MAP,
            'policyQualifiers' => [
                                      'type'     => Identifier::SEQUENCE,
                                      'min'      => 0,
                                      'max'      => -1,
                                      'optional' => true,
                                      'children' => PolicyQualifierInfo::MAP
                                  ]
        ]
    ];
}
