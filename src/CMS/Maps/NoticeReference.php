<?php

/**
 * NoticeReference
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
 * NoticeReference
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class NoticeReference
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'organization'  => DisplayText::MAP,
            'noticeNumbers' => [
                                   'type'     => Identifier::SEQUENCE,
                                   'min'      => 1,
                                   'max'      => 200,
                                   'children' => ['type' => Identifier::INTEGER]
                               ]
        ]
    ];
}
