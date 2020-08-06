<?php
/**
 * RequestModel
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use FG\ASN1\ASN1Object;

abstract class RequestModel extends CMSBase
{
    /**
     * @see https://github.com/mlocati/ocsp
     *
     * @param string[] $urls
     * @return ASN1Object|null
     */
    abstract public function processRequest(array $urls);
}
