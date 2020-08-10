<?php
/**
 * UnsignedAttributes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

class UnsignedAttributes
{
    const MAP = [
        'constant' => 1,
        'optional' => true,
        'implicit' => true
    ] + Attributes::MAP;
}
