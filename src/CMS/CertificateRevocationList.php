<?php

declare(strict_types=1);

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1;
use FG\ASN1\AbstractTime;
use FG\ASN1\Universal\Sequence;

/**
 * Certificate Revocation List (CRL)
 */
class CertificateRevocationList extends CMSBase
{
    /**
     * @param string $content
     * @return CertificateRevocationList
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\CertificateRevocationList::class, Sequence::class));
    }

    /**
     * @return string
     */
    public function getSignatureAlgorithm(): string
    {
        return (string)$this->object->getChildren()[1]->getChildren()[0];
    }

    /**
     * @return string
     */
    public function getSignatureValue(): string
    {
        return (string)$this->object->getChildren()[2];
    }

    /**
     * @return Name
     * @throws Exception
     */
    public function getIssuer(): Name
    {
        $name = $this->getTBSCertList()->findChildrenByType(Sequence::class)[1];

        return new Name($name);
    }

    /**
     * @return ASN1\ASN1Object|mixed
     */
    private function getTBSCertList(): ASN1\ASN1Object
    {
        return $this->object->getChildren()[0];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getThisUpdate(): string
    {
        $time = $this->getTBSCertList()->findChildrenByType(AbstractTime::class)[0];

        return (string)$time;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getNextUpdate(): string
    {
        $time = $this->getTBSCertList()->findChildrenByType(AbstractTime::class)[1];

        return (string)$time;
    }

    /**
     * @return array|string[]
     * @throws Exception
     */
    public function getSerialNumbers(): array
    {
        $revokedCerts = $this->getTBSCertList()->findChildrenByType(Sequence::class)[2] ?? [];

        return array_map(function (Sequence $revokedCert) {
            return gmp_strval((string)$revokedCert->getChildren()[0], 16);
        }, $revokedCerts->getChildren());
    }
}
