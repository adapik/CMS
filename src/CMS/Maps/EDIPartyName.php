<?php

/**
 * EDIPartyName
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
 * EDIPartyName
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class EDIPartyName
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
             'nameAssigner' => [
                                'constant' => 0,
                                'optional' => true,
                                'implicit' => true
                            ] + DirectoryString::MAP,
             // partyName is technically required but \FG\ASN1 doesn't currently support non-optional constants and
             // setting it to optional gets the job done in any event.
             'partyName'    => [
                                'constant' => 1,
                                'optional' => true,
                                'implicit' => true
                            ] + DirectoryString::MAP
        ]
    ];
}
