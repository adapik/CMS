<?php

declare(strict_types=1);

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Sequence;

/**
 * Certificate Revocation List (CRL)
 */
class CertificateRevocationList
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * Certificate constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    private function getTBSCertList()
    {
        return $this->sequence->getChildren()[0];
    }

    public function getSignatureAlgorithm()
    {
        return (string) $this->sequence->getChildren()[1]->getChildren()[0];
    }

    public function getSignatureValue()
    {
        return (string) $this->sequence->getChildren()[2];
    }

    public function getIssuer()
    {
        $name = $this->getTBSCertList()->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[1];

        return new Name($name);
    }

    public function getThisUpdate()
    {
        $time = $this->getTBSCertList()->findChildrenByType(\FG\ASN1\AbstractTime::class)[0];

        return (string) $time;
    }

    public function getNextUpdate()
    {
        $time = $this->getTBSCertList()->findChildrenByType(\FG\ASN1\AbstractTime::class)[1];

        return (string) $time;
    }

    public function getSerialNumbers()
    {
        $revokedCerts = $this->getTBSCertList()->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[2];

        $numbers = array_map(function(Sequence $revokedCert) {
            return gmp_strval((string) $revokedCert->getChildren()[0], 16);
        }, $revokedCerts->getChildren());

        return $numbers;
    }

    /**
     * Constructor
     *
     * @param $content
     *
     * @return self
     *
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1\ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('CRL must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\CertificateList::MAP);

        if ($map === null) {
            throw new FormatException('CRL invalid format');
        }

        return new self($sequence);
    }
}
