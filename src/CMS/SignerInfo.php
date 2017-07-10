<?php

namespace Adapik\CMS;

use FG\ASN1;
use FG\ASN1\Universal\Sequence;

/**
 * SignerInfo
 */
class SignerInfo
{
    const OID_CONTENT_TYPE           = '1.2.840.113549.1.9.3';
    const OID_MESSAGE_DIGEST         = '1.2.840.113549.1.9.4';
    const OID_SIGNING_CERTIFICATE_V2 = '1.2.840.113549.1.9.16.2.47';

    const TYPE_CMS          = 'CMS';
    const TYPE_BES          = 'CAdES-BES';
    const TYPE_T            = 'CAdES-T';
    const TYPE_X_LONG_TYPE1 = 'CAdES-X Long Type 1';

    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * SignerInfo constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * Unsigned Attributes
     * @return \FG\ASN1\ExplicitlyTaggedObject
     */
    public function getUnsignedAttributes()
    {
        $exTaggedObjects = $this->sequence->findChildrenByType(\FG\ASN1\ExplicitlyTaggedObject::class);
        $attributes      = array_filter($exTaggedObjects, function ($value) {
            return $value->identifier->getTagNumber() === 1;
        });
        
        return array_pop($attributes);
    }

    /**
     * Signed Attributes
     * @return \FG\ASN1\ExplicitlyTaggedObject
     */
    public function getSignedAttributes()
    {
        $exTaggedObjects = $this->sequence->findChildrenByType(\FG\ASN1\ExplicitlyTaggedObject::class);
        $attributes      = array_filter($exTaggedObjects, function ($value) {
            return $value->identifier->getTagNumber() === 0;
        });
        
        return array_pop($attributes);
    }

    /**
     * Signature as as a string
     * @return string
     */
    public function getSignatureValue()
    {
        return $this->sequence->findChildrenByType(\FG\ASN1\Universal\OctetString::class)[0]->getBinaryContent();
    }
    
    /**
     * Content type OID
     * @return ASN1\Universal\ObjectIdentifier
     */
    public function getContentType()
    {
        $contentType = $this->getSignedAttributes()->findByOid(self::OID_CONTENT_TYPE);
        if (!empty($contentType)) {
            return $contentType[0]->getSiblings()[0]->findChildrenByType(\FG\ASN1\Universal\ObjectIdentifier::class)[0];
        }
        
        return null;
    }

    /**
     * Signature digest
     * @return ASN1\Universal\OctetString
     */
    public function getMessageDigest()
    {
        $messageDigest = $this->getSignedAttributes()->findByOid(self::OID_MESSAGE_DIGEST);
        if (!empty($messageDigest)) {
            return $messageDigest[0]->getSiblings()[0]->findChildrenByType(\FG\ASN1\Universal\OctetString::class)[0];
        }
        
        return null;
    }

    /**
     * Signing cert
     * @return ASN1\Universal\Set
     */
    public function getSigningCert()
    {
        $signingCert = $this->getSignedAttributes()->findByOid(self::OID_SIGNING_CERTIFICATE_V2);
        if ($signingCert) {
            return $signingCert[0]->getSiblings()[0]->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[0];
        }
        
        return null;
    }

    /**
     * Signing cert digest
     * @return string
     */
    public function getSigningCertDigestValue()
    {
        return $this->getSigningCert()
            ->getChildren()[0]
            ->getChildren()[0]
            ->findChildrenByType(\FG\ASN1\Universal\OctetString::class)[0]
            ->getStringValue();
    }

    /**
     * Signature Timestamp Attribute
     * @return ASN1\Object|null
     */
    public function getTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();
        if ($attributes) {
            $ts = $this->getUnsignedAttributes()->findByOid(TimeStampSignature::getOid());
            if ($ts) {
                return $ts[0]->getSiblings()[0]->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[0];
            }
        }
        return null;
    }

    /**
     * Esc-Timestamp Attribute
     * @return ASN1\Object|null
     */
    public function getEscTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();
        if ($attributes) {
            $ts = $attributes->findByOid(EscTimeStamp::getOid());
            if ($ts) {
                return $ts[0]->getSiblings()[0]->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[0];
            }
        }
        return null;
    }

    /**
     * Has evidences in signature
     * @return bool
     */
    public function hasEvidences()
    {
        $unsignedAttributes = $this->getUnsignedAttributes();
        if ($unsignedAttributes) {
            $revValues  = $unsignedAttributes->findByOid(RevocationValues::getOid());
            $revRefs    = $unsignedAttributes->findByOid(CompleteRevocationRefs::getOid());
            $certValues = $unsignedAttributes->findByOid(CertificateValues::getOid());
            $certRefs   = $unsignedAttributes->findByOid(CompleteCertificateRefs::getOid());
            if (!empty($revValues) && !empty($revRefs) && !empty($certValues) && !empty($certRefs)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Is CAdES-BES
     * @return bool
     */
    private function isBES()
    {
        if ($this->getSigningCert() && $this->getMessageDigest() && $this->getContentType()) {
            return true;
        }
        
        return false;
    }

    /**
     * Is CAdES-T
     * @return bool
     */
    private function isT()
    {
        if ($this->isBES() && $this->getTimeStampToken()) {
            return true;
        }
        
        return false;
    }

    /**
     * Is CAdES-X Long Type 1
     * @return bool
     */
    private function isLongType1()
    {
        if ($this->isBES() && $this->isT() && $this->getEscTimeStampToken() && $this->hasEvidences()) {
            return true;
        }
        
        return false;
    }

    /**
     * Sign algo (oid)
     * @return string
     */
    public function getPublicKeyAlgorithm()
    {
        return $this->sequence
            ->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[2]
            ->getChildren()[0]
            ->getStringValue();
    }

    /**
     * Hash algo (oid)
     * @return string
     */
    public function getDigestAlgorithm()
    {
        return $this->sequence
            ->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[1]
            ->getChildren()[0]
            ->getStringValue();
    }

    /**
     * Define sign format
     * @return string
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
}
