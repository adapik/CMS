<?php
/**
 * SignedDataContent
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class SignedDataContent
 *
 * @see     Maps\SignedDataContent
 * @package Adapik\CMS
 */
class SignedDataContent
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * SignedDataContent constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content
     * @return SignedDataContent
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('SignedDataContent must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\SignedDataContent::MAP);

        if ($map === null) {
            throw new FormatException('SignedDataContent invalid format');
        }

        return new self($sequence);
    }

    public function getDigestAlgorithmIdentifiers()
    {
        $children = $this->sequence->getChildren();

        return;
    }

    /**
     * @return EncapsulatedContentInfo
     * @throws FormatException
     */
    public function getEncapsulatedContentInfo()
    {
        /** @var ExplicitlyTaggedObject $EncapsulatedContentInfoSet */
        $sequence = $this->sequence->findChildrenByType(Sequence::class)[0];

        return EncapsulatedContentInfo::createFromContent($sequence->getBinary());
    }

    /**
     * @return Certificate[]
     * @throws Exception
     */
    public function getCertificateSet()
    {
        $fields = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);

        $certificates = array_filter($fields, function (ASN1Object $field) {
            return $field->getIdentifier()->getTagNumber() === 0;
        });

        if ($certificates) {
            $certificates = array_pop($certificates);
            $certs = $certificates->getChildren();
            foreach ($certs as $cert) {
                /** @var Sequence $cert */
                $x509Certs[] = new Certificate($cert);
            }
            return $x509Certs ?? [];
        }

        return [];
    }

    public function getRevocationInfoChoices()
    {
        $children = $this->sequence->getChildren();

        return;
    }


    /**
     * @return SignerInfo[]
     * @throws Exception
     */
    public function getSignerInfoSet()
    {
        /** @var Set $signerInfoSet */
        $signerInfoSet = $this->sequence->findChildrenByType(Set::class)[1];

        $signerInfoObjects = [];
        foreach ($signerInfoSet->getChildren() as $child) {
            /** @var Sequence $child */
            $SignerInfo = new SignerInfo($child);

            $signerInfoObjects[] = $SignerInfo;
        }
        return $signerInfoObjects;
    }

    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->sequence->getBinary();
    }
}
