<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\SignerInfoInterface;
use Exception;
use FG\ASN1;
use FG\ASN1\AbstractTaggedObject;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\UTCTime;

/**
 * Class SignerInfo
 *
 * @see     Maps\SignerInfo
 * @package Adapik\CMS
 */
class SignerInfo extends CMSBase implements SignerInfoInterface
{
    const OID_CONTENT_TYPE = '1.2.840.113549.1.9.3';
    const OID_MESSAGE_DIGEST = '1.2.840.113549.1.9.4';
    const OID_SIGNING_CERTIFICATE_V2 = '1.2.840.113549.1.9.16.2.47';
    const OID_SIGNING_TIME = "1.2.840.113549.1.9.5";

    const TYPE_CMS = 'CMS';
    const TYPE_BES = 'CAdES-BES';
    const TYPE_T = 'CAdES-T';
    const TYPE_X_LONG_TYPE1 = 'CAdES-X Long Type 1';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return SignerInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\SignerInfo::class, Sequence::class));
    }

    /**
     * Signature as as a hex string
     * @return string
     * @throws Exception
     */
    public function getSignatureValue()
    {
        return bin2hex(
            $this->getSignature()->getBinaryContent()
        );
    }

    /**
     * Signature as independent ASN.1 object
     * @return OctetString|ASN1\ASN1ObjectInterface
     * @throws Exception
     */
    public function getSignature()
    {
        $binary = $this->object->findChildrenByType(OctetString::class)[0]->getBinary();

        return OctetString::fromBinary($binary);
    }

    /**
     * Signing cert hex digest if signingCertificateV2 attribute exist
     * @return string
     * @throws Exception
     */
    public function getSigningCertDigest()
    {
        $signingCertificateV2 = $this->getSigningCertificateV2();

        if (!$signingCertificateV2)
            return null;

        $digest = (string)$signingCertificateV2
            ->getChildren()[1]
            ->getChildren()[0]
            ->getChildren()[0]
            ->getChildren()[0]
            ->findChildrenByType(OctetString::class)[0];

        return bin2hex($digest);
    }

    /**
     * signingCertificateV2  attribute independent from parent if exist
     * @return Sequence|ASN1\ASN1ObjectInterface|null
     * @throws ParserException
     */
    public function getSigningCertificateV2()
    {
        $signingCert = $this->getSignedAttributes()->findByOid(self::OID_SIGNING_CERTIFICATE_V2);
        if ($signingCert) {
            $binary = $signingCert[0]->getParent()->getBinary();

            return Sequence::fromBinary($binary);
        }
        return null;
    }

    /**
     * Signed Attributes without parent reference
     *
     * @return Set|ASN1\ASN1ObjectInterface
     * @throws Exception
     */
    public function getSignedAttributes()
    {
        $exTaggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        /** @var ExplicitlyTaggedObject[] $attributes */
        $attributes = array_filter($exTaggedObjects,
            function ($value) {
                return $value->getIdentifier()->getTagNumber() === 0;
            }
        );

        return $this->convertAttributesToSet(array_pop($attributes));
    }

    /**
     * @param ExplicitlyTaggedObject $attributes
     *
     * @return Set|ASN1ObjectInterface
     * @throws ParserException
     */
    private function convertAttributesToSet(ExplicitlyTaggedObject $attributes)
    {
        // 1. If we return attributes as is - we give reference to parent so any object can be changed directly.
        // That's why we have to create new object from binary
        // 2. When we sign signed attributes, we use Set not ExplicitlyTaggedObject !IMPORTANT
        // @see https://tools.ietf.org/html/rfc5652#section-5.4
        //   A separate encoding
        //   of the signedAttrs field is performed for message digest calculation.
        //   The IMPLICIT [0] tag in the signedAttrs is not used for the DER
        //   encoding, rather an EXPLICIT SET OF tag is used.  That is, the DER
        //   encoding of the EXPLICIT SET OF tag, rather than of the IMPLICIT [0]
        //   tag, MUST be included in the message digest calculation along with
        //   the length and content octets of the SignedAttributes value.

        $binary = $attributes->getBinary();
        $object = ASN1\ASN1Object::fromBinary($binary);

        return Set::create($object->getChildren());
    }

    /**
     * Sign algo (oid)
     * @return string
     * @throws Exception
     */
    public function getPublicKeyAlgorithm()
    {
        return (string)$this->object
            ->findChildrenByType(Sequence::class)[2]
            ->getChildren()[0];
    }

    /**
     * Hash algo (oid)
     * @return string
     * @throws Exception
     */
    public function getDigestAlgorithm()
    {
        return (string)$this->object
            ->findChildrenByType(Sequence::class)[1]
            ->getChildren()[0];
    }

    /**
     * Define sign format
     * @return string
     * @throws Exception
     */
    public function defineType()
    {
        if ($this->isLongType1()) {
            return self::TYPE_X_LONG_TYPE1;
        }

        if ($this->isT()) {
            return self::TYPE_T;
        }

        if ($this->isBES()) {
            return self::TYPE_BES;
        }

        return self::TYPE_CMS;
    }

    /**
     * Is CAdES-X Long Type 1
     * @return bool
     * @throws Exception
     */
    protected function isLongType1()
    {
        if ($this->isBES() && $this->isT() && $this->getUnsignedAttributes()->getEscTimeStamp() && $this->hasEvidences()) {
            return true;
        }

        return false;
    }

    /**
     * Is CAdES-BES
     * @return bool
     * @throws Exception
     */
    protected function isBES()
    {
        if ($this->getSigningCertificateV2() && $this->getMessageDigest() && $this->getContentType()) {
            return true;
        }

        return false;
    }

    /**
     * Signature hex digest
     * @return string
     * @throws Exception
     */
    public function getMessageDigest()
    {
        $messageDigest = $this->getSignedAttributes()->findByOid(self::OID_MESSAGE_DIGEST);
        if (!empty($messageDigest)) {
            $digest = (string)$messageDigest[0]
                ->getSiblings()[0]
                ->findChildrenByType(OctetString::class)[0];


            return bin2hex($digest);
        }

        return null;
    }

    /**
     * Content type OID
     * @return ObjectIdentifier
     * @throws Exception
     */
    protected function getContentType()
    {
        $contentType = $this->getSignedAttributes()->findByOid(self::OID_CONTENT_TYPE);
        if (!empty($contentType)) {
            return $contentType[0]->getSiblings()[0]->findChildrenByType(ObjectIdentifier::class)[0];
        }

        return null;
    }

    /**
     * Is CAdES-T
     * @return bool
     * @throws Exception
     */
    protected function isT()
    {
        if ($this->isBES() && $this->getUnsignedAttributes()->getTimeStampToken()) {
            return true;
        }

        return false;
    }

    /**
     * @return UnsignedAttributes|null
     * @throws Exception
     */
    public function getUnsignedAttributes()
    {
        $unsignedAttributes = $this->findUnsignedAttributes();

        if ($unsignedAttributes) {
            return new UnsignedAttributes($unsignedAttributes);
        } else {
            return null;
        }
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    protected function findUnsignedAttributes()
    {
        $exTaggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        $attributes = array_filter($exTaggedObjects, function ($value) {
            return $value->getIdentifier()->getTagNumber() === 1;
        });

        if (count($attributes) > 0) {
            return array_pop($attributes);
        } else {
            return null;
        }
    }

    /**
     * Has evidences in signature
     * @return bool
     * @throws Exception
     */
    protected function hasEvidences()
    {
        $unsignedAttributes = $this->getUnsignedAttributes();
        if ($unsignedAttributes) {
            $revValues = $unsignedAttributes->getRevocationValues();
            $revRefs = $unsignedAttributes->getRevocationRefs();
            $certValues = $unsignedAttributes->getCertificateValues();
            $certRefs = $unsignedAttributes->getCertificateRefs();
            if (!empty($revValues) && !empty($revRefs) && !empty($certValues) && !empty($certRefs)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns users signing time. Be careful, cause it's users' computer time.
     * @return UTCTime|null
     * @throws ParserException
     */
    public function getSigningTime()
    {
        $SignedTimeStamp = $this->getSignedAttributes()->findByOid(self::OID_SIGNING_TIME);
        if ($SignedTimeStamp) {
            $binary = $SignedTimeStamp[0]->getSiblings()[0]->getChildren()[0]->getBinary();
            return ASN1\Universal\UTCTime::fromBinary($binary);
        }

        return null;
    }

    /**
     * @return IssuerAndSerialNumber|null
     */
    public function getIssuerAndSerialNumber()
    {
        $identifier = $this->object->getChildren()[1];

        // issuerAndSerialNumber
        if ($identifier instanceof Sequence) {
            return new IssuerAndSerialNumber($identifier);
        }

        return null;
    }

    /**
     * @return OctetString|ASN1ObjectInterface|null
     * @throws ParserException
     */
    public function getSubjectKeyIdentifier()
    {
        $identifier = $this->object->getChildren()[1];

        // subjectKeyIdentifier
        if ($identifier instanceof AbstractTaggedObject) {
            $binary = $identifier->getBinary();

            return OctetString::fromBinary($binary);
        }

        return null;
    }

    /**
     * @return ExplicitlyTaggedObject
     * @throws Exception
     */
    protected function getUnsignedAttributesPrivate()
    {
        $exTaggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        $attributes = array_filter($exTaggedObjects, function ($value) {
            return $value->getIdentifier()->getTagNumber() === 1;
        });

        return array_pop($attributes);
    }

    /**
     * Esc-Timestamp Attribute
     * @return ASN1ObjectInterface|Sequence|null
     * @throws Exception
     * @see UnsignedAttributes::getEscTimeStamp()
     * @example $signerInfo->getUnsignedAttributes()->getEscTimeStamp()
     * @deprecated
     */
    protected function getEscTimeStampToken()
    {
        return $this->getUnsignedAttributes()->getEscTimeStamp();
    }
}
