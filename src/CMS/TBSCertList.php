<?php
/**
 * TBSCertList
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
 * Class TBSCertList
 *
 * @see     Maps\TBSCertList
 * @package Adapik\CMS
 */
class TBSCertList extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return TBSCertList
     * @throws FormatException
     */
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\TBSCertList::class, Sequence::class));
    }
}
