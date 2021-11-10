<?php
/**
 * PEMConvertable
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\CMS\Interfaces;

interface PEMConvertable
{
    /**
     * @return string
     */
    public function getPEMHeader(): string;

    /**
     * @return string
     */
    public function getPEMFooter(): string;
}
