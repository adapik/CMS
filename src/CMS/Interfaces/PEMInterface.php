<?php
/**
 * PemInterface
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\CMS\Interfaces;

interface PEMInterface
{
    /**
     * Get in PEM format with header and footer, spliced with 64 symbols chunks
     * @return string
     */
    public function getPEM(): string;
}
