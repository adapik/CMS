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
use Adapik\CMS\Interfaces\CertificateInterface;
use Adapik\CMS\Interfaces\SignerInfoInterface;
use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
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
     *
     * @return SignedDataContent
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\SignedDataContent::class, Sequence::class));
    }

    /**
     * @param SignerInfoInterface|SignerInfo $signerInfo
     *
     * @return Certificate|null
     * @throws ParserException
     */
    public function getCertificateBySignerInfo(SignerInfoInterface $signerInfo)
    {
        $issuerAndSerialNumber = $signerInfo->getIssuerAndSerialNumber();
        $subjectKeyIdentifier = $signerInfo->getSubjectKeyIdentifier();

        foreach ($this->getCertificateSet() as $certificate) {

            if ($subjectKeyIdentifier && $certificate->getSubjectKeyIdentifier() == bin2hex($subjectKeyIdentifier->getBinaryContent())) {
                return $certificate;
            }
            if ($issuerAndSerialNumber && $certificate->getSerial() == $issuerAndSerialNumber->getSerialNumber()) {
                return $certificate;
            }
        }

        return null;
    }

    /**
     * @return Certificate[]
     * @throws Exception
     */
    public function getCertificateSet()
    {
        $certificates = $this->getTaggedObjectByTagNumber(Maps\SignedDataContent::CERTIFICATES_TAG_NUMBER);

        if ($certificates) {
            $x509Certs = [];
            foreach ($certificates->getChildren() as $certificate) {
                $x509Certs[] = new Certificate($certificate);
            }

            return $x509Certs;
        }

        return [];
    }

    /**
     * @param int $tagNumber
     *
     * @return mixed|null
     * @throws Exception
     */
    protected function getTaggedObjectByTagNumber(int $tagNumber)
    {
        $fields = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

        $tag = array_filter($fields,
            function (ASN1Object $field) use ($tagNumber) {
                return $field->getIdentifier()->getTagNumber() === $tagNumber;
            }
        );

        if ($tag) {
            return array_pop($tag);
        }

        return null;
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
     * @see Maps\EncapsulatedContentInfo
     */
    public function getEncapsulatedContentInfo()
    {
        /** @var ExplicitlyTaggedObject $EncapsulatedContentInfoSet */
        $sequence = $this->object->findChildrenByType(Sequence::class)[0];

        return new EncapsulatedContentInfo($sequence);
    }

    /**
     * @return RevocationInfoChoices|null
     * @throws Exception
     */
    public function getRevocationInfoChoices()
    {
        $revs = $this->getTaggedObjectByTagNumber(Maps\SignedDataContent::CLR_TAG_NUMBER);

        if ($revs) {
            return new RevocationInfoChoices($revs);
        }

        return null;
    }

    /**
     * @param CertificateInterface|Certificate $certificate
     *
     * @return SignerInfo|null
     * @throws ParserException
     */
    public function getSignerInfoByCertificate(CertificateInterface $certificate)
    {
        foreach ($this->getSignerInfoSet() as $signerInfo) {

            $issuerAndSerialNumber = $signerInfo->getIssuerAndSerialNumber();
            $subjectKeyIdentifier = $signerInfo->getSubjectKeyIdentifier();

            if ($subjectKeyIdentifier && $certificate->getSubjectKeyIdentifier() == bin2hex($subjectKeyIdentifier->getBinaryContent())) {
                return $signerInfo;
            }

            if ($issuerAndSerialNumber && $certificate->getSerial() == $issuerAndSerialNumber->getSerialNumber()) {
                return $signerInfo;
            }
        }

        return null;
    }

    /**
     * @return SignerInfo[]
     * @throws Exception
     */
    public function getSignerInfoSet()
    {
        /** @var SignerInfo[] $children */
        $children = $this->findSignerInfoChildren();

        array_walk($children,
            function (&$child) {
                $child = new SignerInfo($child);
            }
        );

        return $children;
    }

    /**
     * @return ASN1ObjectInterface[]
     * @throws Exception
     */
    protected function findSignerInfoChildren()
    {
        /** @var Set $signerInfoSet */
        $signerInfoSet = $this->object->findChildrenByType(Set::class)[1];

        $signerInfoObjects = [];
        foreach ($signerInfoSet->getChildren() as $child) {
            //$signerInfoObjects[] = new SignerInfo($child);
            $signerInfoObjects[] = $child;
        }

        return $signerInfoObjects;
    }

    /**
     * @return ASN1Object
     * @throws Exception
     */
    protected function getCertificates()
    {
        $fields = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

        $certificates = array_filter($fields,
            function (ASN1Object $field) {
                return $field->getIdentifier()->getTagNumber() === 0;
            }
        );

        if ($certificates) {
            return array_pop($certificates);
        }

        return null;
    }
}
