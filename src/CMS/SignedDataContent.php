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
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class SignedDataContent
 *
 * @see     Maps\SignedDataContent
 * @package Adapik\CMS
 */
class SignedDataContent extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return SignedDataContent
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\SignedDataContent::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier[]
     */
    public function getDigestAlgorithmIdentifiers()
    {
        $AlgorithmIdentifiers = [];
        $digestAlgorithms = $this->object->getChildren()[1];

        foreach ($digestAlgorithms->getChildren() as $child) {
            $AlgorithmIdentifiers[] = new AlgorithmIdentifier($child);
        }

        return $AlgorithmIdentifiers;
    }

    /**
     * @return EncapsulatedContentInfo
     * @throws Exception
     */
    public function getEncapsulatedContentInfo()
    {
        /** @var ExplicitlyTaggedObject $EncapsulatedContentInfoSet */
        $sequence = $this->object->findChildrenByType(Sequence::class)[0];

        return new EncapsulatedContentInfo($sequence);
    }

    /**
     * @return Certificate[]
     * @throws Exception
     */
    public function getCertificateSet()
    {
        $fields = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

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

    /**
     * TODO: implement and test
     */
    public function getRevocationInfoChoices()
    {
        $children = $this->object->getChildren();

        return;
    }

    /**
     * @return SignerInfo[]
     * @throws Exception
     */
    public function getSignerInfoSet()
    {
        /** @var Set $signerInfoSet */
        $signerInfoSet = $this->object->findChildrenByType(Set::class)[1];

        $signerInfoObjects = [];
        foreach ($signerInfoSet->getChildren() as $child) {
            /** @var Sequence $child */
            $SignerInfo = new SignerInfo($child);

            $signerInfoObjects[] = $SignerInfo;
        }
        return $signerInfoObjects;
    }
}
