<?php
/**
 * CMSInterface
 *
 * @package      Adapik\CMS
 * @copyright    Copyright © Real Time Engineering, LLP - All Rights Reserved
 * @license      Proprietary and confidential
 * Unauthorized copying or using of this file, via any medium is strictly prohibited.
 * Content can not be copied and/or distributed without the express permission of Real Time Engineering, LLP
 *
 * @author       Written by Nurlan Mukhanov <nmukhanov@mp.kz>, июль 2020
 */

namespace Adapik\CMS;

/**
 * Class CMSInterface
 * @package Adapik\CMS
 */
interface CMSInterface
{
    /**
     * @param string $content
     * @return mixed
     */
    public static function createFromContent(string $content);
}
