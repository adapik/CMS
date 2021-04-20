<?php
/**
 * RevocationInfoChoices
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\AbstractTaggedObject;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

/**
 * Class RevocationInfoChoice
 *
 * @see     Maps\RevocationInfoChoices
 * @package Adapik\CMS
 */
class RevocationInfoChoices extends CMSBase
{
    /**
     * @var ExplicitlyTaggedObject
     */
    protected $object;

    /**
     * @param string $content
     * @return RevocationInfoChoices
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\RevocationInfoChoices::class, ExplicitlyTaggedObject::class));
    }

    /**
     * @return CertificateList[]
     */
    public function getCRL(): array
    {
        $crl = [];

        $children = $this->object->getChildren();

        /**
         * In case of certificates, we have 3 sub child
         * @see Maps\CertificateList
         */
        if (count($children) > 0 && $children[0] instanceof Sequence) {
            foreach ($children as $child) {
                $crl[] = new CertificateList($child);
            }
        }

        return $crl;
    }

    /**
     * @note NOT TESTED!
     * @return Sequence[]
     * @throws ParserException
     */
    public function getOther(): array
    {
        $other = [];

        $children = $this->object->getChildren();
        if (count($children) && $children[0] instanceof AbstractTaggedObject) {
            foreach ($children as $child) {
                $binary = $child->getBinaryContent();
                $other[] = Sequence::fromBinary($binary);
            }
        }
        return $other;
    }
}
