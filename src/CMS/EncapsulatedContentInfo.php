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
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class EncapsulatedContentInfo
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
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\EncapsulatedContentInfo::class, Sequence::class));
    }

    /**
     * @return string OID
     */
    public function getContentType(): string
    {
        /** @var ObjectIdentifier $contentType */
        $contentType = $this->object->getChildren()[0];

        return $contentType->__toString();
    }

    /**
     * @return OctetString|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getEContent()
    {
        $return = null;

        $children = $this->object->getChildren();

        if (count($children) == 2) {
            $binary = $children[1]->getChildren()[0]->getBinary();

            $return = OctetString::fromBinary($binary);
        }

        return $return;
    }
}
