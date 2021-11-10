<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use Adapik\CMS\Interfaces\PEMConvertable;
use Exception;
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
class SignedData extends CMSBase implements PEMConvertable
{
    const OID_DATA = '1.2.840.113549.1.7.1';
    const PEM_HEADER = "BEGIN CMS";
    const PEM_FOOTER = "END CMS";

    /**
     * @param string $content
     * @return SignedData
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
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
     * @noinspection PhpMissingReturnTypeInspection
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
    public function hasData(): bool
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
    public function getData(): ?string
    {
        $siblings = $this->object->findByOid(self::OID_DATA)[0]->getSiblings();
        /** @var ExplicitlyTaggedObject|null $dataValue */
        $dataValue = count($siblings) > 0 ? $siblings[0] : null;

        if (null === $dataValue || $dataValue instanceof NullObject) {
            return null;
        }
        /** @var OctetString $octetString */
        $octetString = $dataValue->getChildren()[0];
        $data = $octetString->getBinaryContent();
        if ($octetString->isConstructed()) {
            $data = '';
            foreach ($octetString->getChildren() as $child) {
                /** @var OctetString $child */
                $data .= $child->getBinaryContent();
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPEMHeader(): string
    {
        return self::PEM_HEADER;
    }

    /**
     * @return string
     */
    public function getPEMFooter(): string
    {
        return self::PEM_FOOTER;
    }
}
