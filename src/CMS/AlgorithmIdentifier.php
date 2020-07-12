<?php
/**
 * AlgorithmIdentifier
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\Sequence;

/**
 * Class AlgorithmIdentifier
 *
 * @see     Maps\AlgorithmIdentifier
 * @package Adapik\CMS
 */
class AlgorithmIdentifier extends CMSBase
{
    /**
     * @param string $content
     * @return AlgorithmIdentifier
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\AlgorithmIdentifier::class, Sequence::class));
    }
}
