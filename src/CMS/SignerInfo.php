<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class SignerInfo
 *
 * @see     Maps\SignerInfo
 * @package Adapik\CMS
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
     * @return ExplicitlyTaggedObject
     * @throws Exception
     */
    public function getUnsignedAttributes()
    {
        $exTaggedObjects = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        $attributes      = array_filter($exTaggedObjects, function ($value) {
            return $value->getIdentifier()->getTagNumber() === 1;
        });
        
        return array_pop($attributes);
    }

    /**
     * Signed Attributes
     * @return ExplicitlyTaggedObject
     * @throws Exception
     */
    public function getSignedAttributes()
    {
        $exTaggedObjects = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        $attributes      = array_filter($exTaggedObjects, function ($value) {
            return $value->getIdentifier()->getTagNumber() === 0;
        });
        
        return array_pop($attributes);
    }

    /**
     * Signature as as a hex string
     * @return string
     * @throws Exception
     */
    public function getSignatureValue()
    {
        return bin2hex(
            $this->sequence->findChildrenByType(OctetString::class)[0]->getBinaryContent()
        );
    }

    /**
     * @return OctetString
     * @throws Exception
     */
    public function getSignature() {
        return $this->sequence->findChildrenByType(OctetString::class)[0];
    }

    /**
     * Content type OID
     * @return ObjectIdentifier
     * @throws Exception
     */
    private function getContentType()
    {
        $contentType = $this->getSignedAttributes()->findByOid(self::OID_CONTENT_TYPE);
        if (!empty($contentType)) {
            return $contentType[0]->getSiblings()[0]->findChildrenByType(ObjectIdentifier::class)[0];
        }
        
        return null;
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
            $digest = (string) $messageDigest[0]
                ->getSiblings()[0]
                ->findChildrenByType(OctetString::class)[0];


            return bin2hex($digest);
        }
        
        return null;
    }

    /**
     * Signing cert
     * @return ASN1\Universal\Set
     * @throws Exception
     */
    private function getSigningCert()
    {
        $signingCert = $this->getSignedAttributes()->findByOid(self::OID_SIGNING_CERTIFICATE_V2);
        if ($signingCert) {
            return $signingCert[0]->getSiblings()[0]->findChildrenByType(Sequence::class)[0];
        }
        
        return null;
    }

    /**
     * Signing cert hex digest
     * @return string
     * @throws Exception
     */
    public function getSigningCertDigest()
    {
        $digest = (string) $this->getSigningCert()
            ->getChildren()[0]
            ->getChildren()[0]
            ->findChildrenByType(OctetString::class)[0];

        return bin2hex($digest);
    }

    /**
     * Signature Timestamp Attribute
     * @return ASN1\Object|null
     * @throws Exception
     */
    private function getTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();
        if ($attributes) {
            $ts = $this->getUnsignedAttributes()->findByOid(TimeStampToken::getOid());
            if ($ts) {
                return $ts[0]->getSiblings()[0]->findChildrenByType(Sequence::class)[0];
            }
        }

        return null;
    }

    /**
     * Esc-Timestamp Attribute
     * @return ASN1\Object|null
     * @throws Exception
     */
    private function getEscTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();
        if ($attributes) {
            $ts = $attributes->findByOid(EscTimeStamp::getOid());
            if ($ts) {
                return $ts[0]->getSiblings()[0]->findChildrenByType(Sequence::class)[0];
            }
        }

        return null;
    }

    /**
     * Has evidences in signature
     * @return bool
     * @throws Exception
     */
    private function hasEvidences()
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function getPublicKeyAlgorithm()
    {
        return (string) $this->sequence
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
        return (string) $this->sequence
            ->findChildrenByType(Sequence::class)[1]
            ->getChildren()[0];
    }

    /**
     * @return string
     */
    public function getBinary(): string
    {
        return $this->sequence->getBinary();
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
     * Sometimes having Cryptographic Message Syntax (CMS) we need to store OCSP check response for the
     * signer certificate, otherwise CMS data means nothing.
     *
     * @param BasicOCSPResponse[] $basicOCSPResponses
     * @return bool
     * @throws Exception
     */
    public function addUnsignedRevocationValues(array $basicOCSPResponses)
    {
        /**
         * 1. Get or create unsigned attributes
         */
        list($UnsignedAttribute, $unsignedSelfCreated) = $this->getOrCreateUnsignedAttributes();

        /**
         * 2. Now check do we have to check existence of 1.2.840.113549.1.9.16.2.24 in attributes
         */
        // FIXME: добавлять в массив
        if (count($UnsignedAttribute->findByOid(RevocationValues::getOid())) > 0) {
            throw new Exception("You already have RevocationValues in UnsignedAttributes");
        }

        /**
         * 3. Now if we don't have, lets create RevocationValues object
         */
        $responses = [];
        foreach ($basicOCSPResponses as $basicOCSPResponse) {
            $binary = $basicOCSPResponse->getBinary();
            $responses[] = ASN1\ASN1Object::fromBinary($binary);
        }

        $revocationValues = Sequence::create([
            ObjectIdentifier::create(RevocationValues::getOid()),
            Set::create([
                Sequence::create([
                    ExplicitlyTaggedObject::create(1, Sequence::create($responses))
                ])
            ])
        ]);

        /**
         * 4. Finally insert it into $UnsignedAttribute.
         */
        if ($unsignedSelfCreated) {
            $UnsignedAttribute->replaceChild(0, $revocationValues);
        } else {
            $UnsignedAttribute->appendChild($revocationValues);
        }

        return true;
    }

    /**
     * @return ASN1\ImplicitlyTaggedObject
     */
    private function createUnsignedAttribute()
    {
        return ExplicitlyTaggedObject::create(1, ASN1\Universal\NullObject::create());
    }

    /**
     * @return TimeStampToken|null
     * @throws FormatException
     */
    public function getUnsignedTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();

        if ($attributes) {
            $rv = $attributes->findByOid(TimeStampToken::getOid());

            if ($rv) {
                return TimeStampToken::createFromContent($rv[0]->getParent()->getBinary());
            }
        }

        return null;
    }

    /**
     * @return RevocationValues[]|null
     * @throws Exception|FormatException
     */
    public function getUnsignedRevocationValues()
    {
        $attributes = $this->getUnsignedAttributes();
        $values = [];
        if ($attributes) {
            $rv = $attributes->findByOid(RevocationValues::getOid());

            if ($rv) {
                /** @var Set $set */
                $set = $rv[0]->getParent()->findChildrenByType(Set::class)[0];

                /** @var Sequence $child */
                foreach ($set->getChildren() as $child) {
                    $content = $child->getBinary();
                    $values[] = RevocationValues::createFromContent($content);
                }
                return $values;
            }
        }

        return [];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getOrCreateUnsignedAttributes(): array
    {
        /**
         * 1. First check do we have unsignedAttrs or not, cause it is optional fields and create it if not.
         * Always push it to the end of child.
         */
        $UnsignedAttribute = $this->getUnsignedAttributes();

        $unsignedSelfCreated = false;

        if (is_null($UnsignedAttribute)) {
            $UnsignedAttribute = $this->createUnsignedAttribute();
            $unsignedSelfCreated = true;
            $this->sequence->appendChild($UnsignedAttribute);
        }

        $UnsignedAttribute = $this->getUnsignedAttributes();

        return array($UnsignedAttribute, $unsignedSelfCreated);
    }

    /**
     * This function will append TimeStampToken with TSTInfo or create TimeStampToken as UnsignedAttribute
     *
     * @param TimeStampResponse[] $timeStampResponses
     * @throws Exception
     */
    public function addUnsignedTimeStampToken(array $timeStampResponses)
    {
        /** @var bool $unsignedSelfCreated */
        list($UnsignedAttribute, $unsignedSelfCreated) = $this->getOrCreateUnsignedAttributes();

        /**
         * 2. Now check do we have to check existence of 1.2.840.113549.1.9.16.2.14 in attributes
         */
        $timeStampTokenSearch = $UnsignedAttribute->findByOid(TimeStampToken::getOid());
        if (count($timeStampTokenSearch) > 0) {

            $set = $timeStampTokenSearch[0]->getParent()->getChildren()[1];

            foreach ($timeStampResponses as $timeStampResponse) {
                $binary = $timeStampResponse->getTimeStampToken()->getBinary();
                $set->appendChild(ASN1\ASN1Object::fromBinary($binary));
            }

        } else {
            $timeStampToken = TimeStampToken::createEmpty();
            $timeStampToken->getChildren()[1]->getChildren()[0]->remove();

            foreach ($timeStampResponses as $timeStampResponse) {
                $binary = $timeStampResponse->getTimeStampToken()->getBinary();
                $timeStampToken->getChildren()[1]->appendChild(ASN1\ASN1Object::fromBinary($binary));
            }

            if ($unsignedSelfCreated) {
                $UnsignedAttribute->replaceChild(0, $timeStampToken);
            } else {
                $UnsignedAttribute->appendChild($timeStampToken);
            }
        }

        return;
    }

    /**
     * @param int $index
     * @param TimeStampResponse $timeStampResponse
     * @throws ASN1\Exception\ParserException
     */
    public function replaceUnsignedTimeStampToken(int $index, TimeStampResponse $timeStampResponse) {
        $UnsignedAttribute = $this->getUnsignedAttributes();

        $timeStampTokenSearch = $UnsignedAttribute->findByOid(TimeStampToken::getOid());

        if (count($timeStampTokenSearch) == 0) {
            throw new Exception("No TimeStampToken found");
        }

        $set = $timeStampTokenSearch[0]->getParent()->getChildren()[1];
        $binary = $timeStampResponse->getTimeStampToken()->getBinary();
        $set->replaceChild($index, ASN1\ASN1Object::fromBinary($binary));

        return;
    }

}
