<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class SignedData
 *
 * @see     Maps\SignedData
 * @package Adapik\CMS
 */
class SignedData extends CMSBase
{
    const OID_DATA = '1.2.840.113549.1.7.1';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return SignedData
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\SignedData::class, Sequence::class));
    }

    /**
     * SignerInfo of this message
     * @return SignerInfo[]
     * @throws Exception
     * @deprecated
     * @see SignedDataContent::getSignerInfoSet()
     */
    public function getSignerInfo(): array
    {
        return $this->getSignedDataContent()->getSignerInfoSet();
    }

    /**
     * Message content
     * @return SignedDataContent
     * @throws Exception
     */
    public function getSignedDataContent()
    {
        $SignedDataContent = $this->object->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return new SignedDataContent($SignedDataContent->getChildren()[0]);
    }

    /**
     * Certificates of this message
     * @return Certificate[]
     * @throws Exception
     * @deprecated
     * @see SignedDataContent::getCertificateSet()
     */
    public function extractCertificates(): array
    {
        return $this->getSignedDataContent()->getCertificateSet();
    }

    /**
     * If there is a signed content
     * @return bool
     */
    public function hasData()
    {
        $siblings = $this->object->findByOid(self::OID_DATA)[0]->getSiblings();
        /** @var ExplicitlyTaggedObject|null $dataValue */
        $dataValue = !empty($siblings) ? $siblings[0] : null;
        if (null === $dataValue || $dataValue instanceof NullObject) {
            return false;
        }

        return true;
    }

    /**
     * Get signed content as binary string
     * @return null|string
     */
    public function getData()
    {
        $siblings = $this->object->findByOid(self::OID_DATA)[0]->getSiblings();
        /** @var ExplicitlyTaggedObject|null $dataValue */
        $dataValue = count($siblings) > 0 ? $siblings[0] : null;

        if (null === $dataValue || $dataValue instanceof NullObject) {
            return null;
        }
        /** @var OctetString $octetString */
        $octetString = $dataValue->getChildren()[0];
        $data = '';
        if ($octetString->isConstructed()) {
            foreach ($octetString->getChildren() as $child) {
                /** @var OctetString $child */
                $data .= $child->getBinaryContent();
            }
        } else {
            $data = $octetString->getBinaryContent();
        }
        return $data;
    }

    /**
     * @param SignedData $signedData
     * @return $this
     * @throws ParserException
     * @todo move to extended package
     */
    public function mergeCMS(SignedData $signedData) {
        $initialContent = $this->getSignedDataContent();
        $newContent = $signedData->getSignedDataContent();
        /*
         * Append
         * 1. digestAlgorithms
         * 2. certificates
         * 3. crl
         * 4. signerInfos
         */

        foreach ($newContent->getDigestAlgorithmIdentifiers() as $digestAlgorithmIdentifier) {
            $initialContent->appendDigestAlgorithmIdentifier($digestAlgorithmIdentifier);
        }

        foreach ($newContent->getCertificateSet() as $certificate) {
            $initialContent->appendCertificate($certificate);
        }

        foreach ($newContent->getSignerInfoSet() as $signerInfo) {
            $initialContent->appendSignerInfo($signerInfo);
        }

        return $this;

    }
}
