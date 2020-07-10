<?php
/**
 * TimeStampResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class TimeStampResponse
{
    /**
     * TimeStampResp ::= SEQUENCE  {
     *      status                  PKIStatusInfo,
     *      timeStampToken          TimeStampToken     OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'status' => PKIStatusInfo::MAP,
            'timeStampToken' => ['optional' => true ] + SignedData::MAP,
        ]
    ];
}