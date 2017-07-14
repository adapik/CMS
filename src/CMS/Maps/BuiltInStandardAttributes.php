<?php

/**
 * BuiltInStandardAttributes
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
 * BuiltInStandardAttributes
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class BuiltInStandardAttributes
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'country-name'               => ['optional' => true] + CountryName::MAP,
            'administration-domain-name' => ['optional' => true] + AdministrationDomainName::MAP,
            'network-address'            => [
                                             'constant' => 0,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + NetworkAddress::MAP,
            'terminal-identifier'        => [
                                             'constant' => 1,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + TerminalIdentifier::MAP,
            'private-domain-name'        => [
                                             'constant' => 2,
                                             'optional' => true,
                                             'explicit' => true
                                           ] + PrivateDomainName::MAP,
            'organization-name'          => [
                                             'constant' => 3,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + OrganizationName::MAP,
            'numeric-user-identifier'    => [
                                             'constant' => 4,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + NumericUserIdentifier::MAP,
            'personal-name'              => [
                                             'constant' => 5,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + PersonalName::MAP,
            'organizational-unit-names'  => [
                                             'constant' => 6,
                                             'optional' => true,
                                             'implicit' => true
                                           ] + OrganizationalUnitNames::MAP
        ]
    ];
}
