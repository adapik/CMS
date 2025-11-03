<?php
/**
 * PEMConverter
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\PEMConvertable;

class PEMConverter
{
    /**
     * Get in PEM format with header and footer, spliced with 64 symbols chunks
     *
     * @param PEMConvertable $object
     * @return string
     */
    public static function toPEM(PEMConvertable $object): string
    {
        $pem = rtrim(chunk_split(base64_encode($object->getBinary()), 64));

        return sprintf("-----%s-----\r\n%s\r\n-----%s-----\r\n", $object->getPEMHeader(), $pem, $object->getPEMFooter());
    }
}
