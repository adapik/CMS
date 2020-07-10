<?php
/**
 * RevocationValues
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Sequence;

/**
 * Class RevocationValues
 *
 * @see     Maps\RevocationValues
 * @package Adapik\CMS
 */
class RevocationValues extends UnsignedAttribute
{
    /**
     * @var Sequence
     */
    protected $sequence;

    protected static $oid = '1.2.840.113549.1.9.16.2.24';

    /**
     * RevocationValues constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content
     *
     * @return RevocationValues
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('RevocationValues must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\RevocationValues::MAP);

        if ($map === null) {
            throw new FormatException('RevocationValues invalid format');
        }

        return new self($sequence);
    }

    public function getCertificateList()
    {
        /** @var ExplicitlyTaggedObject $tagged */
        $tagged = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);

        foreach ($tagged as $object) {
            if ($object->getIdentifier()->getTagNumber() == 0) {

                /** @var CertificateList[] $CertificateList */
                $CertificateList = [];

                /** @var Sequence $children */
                $children = $object->getChildren();

                foreach ($children as $child) {
                    $CertificateList[] = CertificateList::createFromContent($child->getBinaryContent());
                }

                return $CertificateList;
            }
        }

        return null;
    }

    /**
     * @return BasicOCSPResponse[]|null
     * @throws FormatException
     */
    public function getBasicOCSPResponses()
    {
        /** @var ExplicitlyTaggedObject $tagged */
        $tagged = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);

        foreach ($tagged as $object) {
            if ($object->getIdentifier()->getTagNumber() == 1) {

                /** @var BasicOCSPResponse[] $BasicOCSPResponses */
                $BasicOCSPResponses = [];

                /** @var Sequence $children */
                $children = $object->getChildren();

                foreach ($children as $child) {
                    $BasicOCSPResponses[] = BasicOCSPResponse::createFromContent($child->getBinaryContent());
                }

                return $BasicOCSPResponses;
            }
        }

        return null;
    }
}
