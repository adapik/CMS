<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\OctetString;
use FG\ASN1;

/**
 * Class SignedData
 *
 * @see     Maps\SignedData
 * @package Adapik\CMS
 */
class SignedData
{
    const OID_DATA = '1.2.840.113549.1.7.1';

    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * Message content
     * @return SignedDataContent
     * @throws Exception
     */
    public function getSignedDataContent()
    {
        $SignedDataContent = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return new SignedDataContent($SignedDataContent->getChildren()[0]);
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
     * Certificates of this message
     * @deprecated
     * @see SignedDataContent::getCertificateSet()
     * @return Certificate[]
     * @throws Exception
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
        $siblings = $this->sequence->findByOid(self::OID_DATA)[0]->getSiblings();
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
        $siblings = $this->sequence->findByOid(self::OID_DATA)[0]->getSiblings();
        /** @var ExplicitlyTaggedObject|null $dataValue */
        $dataValue = count($siblings) > 0 ? $siblings[0] : null;

        if (null === $dataValue || $dataValue instanceof NullObject) {
            return null;
        }
        /** @var OctetString $octetString */
        $octetString = $dataValue->getChildren()[0];
        $data        = '';
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
     * @return string
     */
    public function getBinary(): string
    {
        return $this->sequence->getBinary();
    }

    /**
     * Конструктор из бинарных данных
     *
     * @param $content
     *
     * @return SignedData
     *
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1\ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('SignedData must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\SignedData::MAP);

        if ($map === null) {
            throw new FormatException('SignedData invalid format');
        }

        return new self($sequence);
    }

    /**
     * Removing content if exist in case of necessity.
     * Actually we sign content hash and storing content not always strict.
     * Moreover content can be very huge and heavy
     */
    public function removeEncapsulatedContentInfoEContent() {
        $data = $this->sequence->findByOid(self::OID_DATA);
        $content = $data[0];

        $siblings = $content->getSiblings();
        if (count($siblings) > 0) {
            $siblings[0]->remove();
        }

        return;
    }
}
