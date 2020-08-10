<?php
/**
 * Extension
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
 * Extension
 *
 * A certificate using system MUST reject the certificate if it encounters
 * a critical extension it does not recognize; however, a non-critical
 * extension may be ignored if it is not recognized.
 *
 * http://tools.ietf.org/html/rfc5280#section-4.2
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class Extension
{
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'extensionId' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'isCritical' => [
                'type' => Identifier::BOOLEAN,
                'optional' => true,
                'default' => false
            ],
            'extensionValue' => ['type' => Identifier::OCTETSTRING]
        ]
    ];
}
