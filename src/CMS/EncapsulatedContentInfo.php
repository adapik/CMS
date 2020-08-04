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
use FG\ASN1\Exception\Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
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
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\EncapsulatedContentInfo::class, Sequence::class));
    }

    /**
     * @return string OID
     */
    public function getContentType()
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
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            $binary = $children[1]->getChildren()[0]->getBinary();

            return OctetString::fromBinary($binary);
        }

        return null;
    }

    /**
     * Insert or update data content
     *
     * @param OctetString $octetString
     * @return $this
     * @throws Exception
     * @todo Move to extended package
     */
    public function setEContent(OctetString $octetString)
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            $this->object->replaceChild($children[1], $octetString);
        } else {
            $this->object->appendChild(ExplicitlyTaggedObject::create(0, $octetString));
        }

        return $this;
    }

    /**
     * Removing content if exist in case of necessity.
     * Actually we sign content hash and storing content not always strict.
     * Moreover content can be very huge and heavy
     *
     * @return $this
     * @throws Exception
     * @todo Move to extended package
     */
    public function unSetEContent()
    {
        $children = $this->object->getChildren();
        if (count($children) == 2) {
            $eContent = $children[1];
            $this->object->removeChild($eContent);
        }

        return $this;
    }
}
