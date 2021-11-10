<?php
/**
 * PEMBase
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\PEMInterface;

abstract class PEMBase extends CMSBase implements PEMInterface
{
    const PEM_HEADER = "PLEASE REDEFINE ME";
    const PEM_FOOTER = "PLEASE REDEFINE ME";

    /**
     * @return string
     * @inheritdoc
     */
    public function getPEM(): string
    {
        $pem = chunk_split(base64_encode($this->getBinary()), 64);

        return sprintf("-----%s-----\r\n%s\r\n-----%s-----\r\n", $this::PEM_HEADER, $pem, $this::PEM_FOOTER);
    }
}
