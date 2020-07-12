<?php
/**
 * EncapsulatedContentInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class EncapsulatedContentInfo.
 * @see     Maps\EncapsulatedContentInfo
 * @package Adapik\CMS
 */
class EncapsulatedContentInfo extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return EncapsulatedContentInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\EncapsulatedContentInfo::class, Sequence::class));
    }

    /**
     * @return OctetString[]
     * @throws ParserException
     */
    public function getEContent()
    {
        $eContentSet = $this->object->getChildren()[1];

        $OctetStrings = [];
        foreach ($eContentSet->getChildren() as $octetString) {
            $binary = $octetString->getBinary();
            $OctetStrings[] = OctetString::fromBinary($binary);
        }

        return $OctetStrings;
    }
}
