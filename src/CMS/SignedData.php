<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\OctetString;
use FG\ASN1;

/**
 * SignedData
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
     * @return ExplicitlyTaggedObject
     * @throws \Exception
     */
    public function getSignedDataContent()
    {
        return $this->sequence->findChildrenByType(\FG\ASN1\ExplicitlyTaggedObject::class)[0];
    }

    /**
     * SignerInfo of this message
     * @return SignerInfo[]
     */
    public function getSignerInfo(): array
    {
        /** @var Set $signerInfoSet */
        $signerInfoSet = $this->getSignedDataContent()
            ->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[0]
            ->findChildrenByType(\FG\ASN1\Universal\Set::class)[1];

        $signerInfoObjects = [];
        foreach ($signerInfoSet->getChildren() as $child) {
            /** @var Sequence $child */
            $signerInfoObjects[] = new SignerInfo($child);
        }
        return $signerInfoObjects;
    }

    /**
     * Certificates of this message
     * @return Certificate[]
     * @throws \Exception
     */
    public function extractCertificates(): array
    {
        $fields = $this->getSignedDataContent()
            ->getChildren()[0]
            ->findChildrenByType(\FG\ASN1\ExplicitlyTaggedObject::class);
        $certificates = array_filter($fields, function(ASN1\ASN1Object $field) {
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

        $map = (new Mapper())->map($sequence, \Adapik\CMS\Maps\SignedData::MAP);

        if ($map === null) {
            throw new FormatException('SignedData invalid format');
        }

        return new self($sequence);
    }
}
