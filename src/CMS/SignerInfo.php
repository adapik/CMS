<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1;
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
class SignerInfo extends CMSBase
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
        if ($this->isBES() && $this->isT() && $this->getEscTimeStampToken() && $this->hasEvidences()) {
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
        if ($this->isBES() && $this->getUnsignedTimeStampToken()) {
            return true;
        }

        return false;
    }

    /**
     * @return TimeStampToken|null
     * @throws Exception
     */
    public function getUnsignedTimeStampToken()
    {
        $attributes = $this->getUnsignedAttributes();

        if ($attributes) {
            $rv = $attributes->findByOid(TimeStampToken::getOid());

            if ($rv) {
                return new TimeStampToken($rv[0]->getParent());
            }
        }

        return null;
    }

    /**
     * Unsigned Attributes without parent reference
     *
     * @return Set|ASN1\ASN1ObjectInterface
     * @throws Exception
     */
    public function getUnsignedAttributes()
    {
        return $this->convertAttributesToSet($this->getUnsignedAttributesPrivate());
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
     * @return ASN1\Object|null
     * @throws Exception
     */
    protected function getEscTimeStampToken()
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
    protected function hasEvidences()
    {
        $unsignedAttributes = $this->getUnsignedAttributes();
        if ($unsignedAttributes) {
            $revValues = $unsignedAttributes->findByOid(RevocationValues::getOid());
            $revRefs = $unsignedAttributes->findByOid(CompleteRevocationRefs::getOid());
            $certValues = $unsignedAttributes->findByOid(CertificateValues::getOid());
            $certRefs = $unsignedAttributes->findByOid(CompleteCertificateRefs::getOid());
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
     * Sometimes having Cryptographic Message Syntax (CMS) we need to store OCSP check response for the
     * signer certificate, otherwise CMS data means nothing.
     *
     * @param BasicOCSPResponse[] $basicOCSPResponses
     * @return bool
     * @throws Exception
     * @todo move to extended package
     */
    public function addUnsignedRevocationValues(array $basicOCSPResponses)
    {
        /**
         * 1. create unsigned attributes
         */
        $this->createUnsignedAttributesIfNotExist();

        /**
         * 2. Now if we don't have, lets create RevocationValues object
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

        $this->getUnsignedAttributesPrivate()->appendChild($revocationValues);

        return true;
    }

    /**
     * @return void
     * @throws Exception
     * @todo move to extended package
     */
    protected function createUnsignedAttributesIfNotExist(): void
    {
        /**
         * 1. First check do we have unsignedAttrs or not, cause it is optional fields and create it if not.
         * Always push it to the end of child.
         */
        $UnsignedAttribute = $this->getUnsignedAttributes();

        if (is_null($UnsignedAttribute)) {
            $UnsignedAttribute = $this->createUnsignedAttribute();
            $this->object->appendChild($UnsignedAttribute);
        }

        return;
    }

    /**
     * @return ASN1\ImplicitlyTaggedObject
     * @todo move to extended package
     */
    protected function createUnsignedAttribute()
    {
        return ExplicitlyTaggedObject::create(1, ASN1\Universal\NullObject::create());
    }

    /**
     * @return RevocationValues[]|null
     * @throws Exception
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
                    $values[] = new RevocationValues($child);
                }
                return $values;
            }
        }

        return [];
    }

    /**
     * This function will append TimeStampToken with TSTInfo or create TimeStampToken as UnsignedAttribute
     *
     * @param TimeStampResponse[] $timeStampResponses
     * @throws Exception
     * @todo move to extended package
     */
    public function addUnsignedTimeStampToken(array $timeStampResponses)
    {
        $this->createUnsignedAttributesIfNotExist();

        /**
         * 2. Now check do we have to check existence of 1.2.840.113549.1.9.16.2.14 in attributes
         */
        $timeStampTokenSearch = $this->getUnsignedAttributesPrivate()->findByOid(TimeStampToken::getOid());
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

            $this->getUnsignedAttributesPrivate()->appendChild($timeStampToken);

        }

        return;
    }

    /**
     * FIXME: replaceChild
     * @param TimeStampResponse $oldTimeStampResponse
     * @param TimeStampResponse $newTimeStampResponse
     * @throws ASN1\Exception\Exception
     * @throws ParserException
     * @todo move to extended package
     */
    public function replaceUnsignedTimeStampToken(TimeStampResponse $oldTimeStampResponse, TimeStampResponse $newTimeStampResponse)
    {
        $UnsignedAttribute = $this->getUnsignedAttributesPrivate();

        $timeStampTokenSearch = $UnsignedAttribute->findByOid(TimeStampToken::getOid());

        if (count($timeStampTokenSearch) == 0) {
            throw new Exception("No TimeStampToken found");
        }

        $set = $timeStampTokenSearch[0]->getParent()->getChildren()[1];
        $binary = $newTimeStampResponse->getTimeStampToken()->getBinary();
        $set->replaceChild($oldTimeStampResponse, ASN1\ASN1Object::fromBinary($binary));

        return;
    }
}
