<?php

/**
 * DistributionPointName
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
 * DistributionPointName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class DistributionPointName
{
    const MAP = [
        'type'     => Identifier::CHOICE,
        'children' => [
            'fullName'                => [
                                             'constant' => 0,
                                             'optional' => true,
                                             'implicit' => true
                                   ] + GeneralNames::MAP,
            'nameRelativeToCRLIssuer' => [
                                             'constant' => 1,
                                             'optional' => true,
                                             'implicit' => true
                                   ] + RelativeDistinguishedName::MAP
        ]
    ];
}
