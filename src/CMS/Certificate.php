<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CertificateInterface;
use Exception;
use FG\ASN1;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Certificate
 *
 * @see     Maps\Certificate
 * @package Adapik\CMS
 */
class Certificate extends CMSBase implements CertificateInterface
{
    const OID_EXTENSION_SUBJECT_KEY_ID = '2.5.29.14';
    const OID_EXTENSION_BASIC_CONSTRAINTS = '2.5.29.19';
    const OID_EXTENSION_AUTHORITY_KEY_ID = '2.5.29.35';
    const OID_EXTENSION_AUTHORITY_INFO_ACCESS = '1.3.6.1.5.5.7.1.1';
    const OID_OCSP_URI = '1.3.6.1.5.5.7.48.1';
    const OID_EXTENSION_CERT_POLICIES = '2.5.29.32';
    const OID_EXTENSION_KEY_USAGE = '2.5.29.15';
    const OID_EXTENSION_EXTENDED_KEY_USAGE = '2.5.29.37';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * Конструктор из бинарных данных
     *
     * @param string $content
     * @return Certificate
     * @throws FormatException
     */
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\Certificate::class, Sequence::class));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSerial(): string
    {
        return (string)$this->getTBSCertificate()->findChildrenByType(Integer::class)[0];
    }

    /**
     * @return Sequence
     * @throws Exception
     */
    protected function getTBSCertificate(): Sequence
    {
        return $this->object->findChildrenByType(Sequence::class)[0];
    }

    /**
     * @return string
     * @throws ASN1\Exception\ParserException
     */
    public function getSubjectKeyIdentifier(): string
    {
        $content = $this->getExtension(self::OID_EXTENSION_SUBJECT_KEY_ID);

        return bin2hex($content->getBinaryContent());
    }

    /**
     * @param string $oidString
     *
     * @return ASN1\ASN1ObjectInterface|null
     * @throws ASN1\Exception\ParserException
     */
    protected function getExtension(string $oidString): ?ASN1Object
    {
        $return = null;
        $oid = $this->getExtensions()->findByOid($oidString);

        if ($oid) {
            $value = $oid[0]->getParent()->findChildrenByType(OctetString::class)[0]->getBinaryContent();
            $return = ASN1\ASN1Object::fromBinary($value);
        }

        return $return;
    }

    /**
     * @return ExplicitlyTaggedObject
     * @throws Exception
     */
    private function getExtensions(): ExplicitlyTaggedObject
    {
        $exTaggedObjects = $this->getTBSCertificate()->findChildrenByType(ExplicitlyTaggedObject::class);
        $extensions = array_filter($exTaggedObjects, function (ASN1\ASN1Object $value) {
            return $value->getIdentifier()->getTagNumber() === 3;
        });

        return array_pop($extensions);
    }

    /**
     * @return string|null
     * @throws ASN1\Exception\ParserException
     */
    public function getAuthorityKeyIdentifier(): ?string
    {
        $return = null;

        $content = $this->getExtension(self::OID_EXTENSION_AUTHORITY_KEY_ID);

        if (!is_null($content)) {
            $children = $content->findChildrenByType(ImplicitlyTaggedObject::class);
            $children = array_filter($children, function (ASN1\ASN1Object $value) {
                return $value->getIdentifier()->getTagNumber() === 0;
            });

            $return = bin2hex($children[0]->getBinaryContent());
        }

        return $return;
    }

    /**
     * @return string[]
     * @throws ASN1\Exception\ParserException
     * @noinspection DuplicatedCode
     */
    public function getOcspUris(): array
    {
        $uris = [];

        $content = $this->getExtension(self::OID_EXTENSION_AUTHORITY_INFO_ACCESS);

        if (!is_null($content)) {
            $oids = $content->findByOid(self::OID_OCSP_URI);

            if ($oids) {
                $uris = [];
                foreach ($oids as $oid) {
                    $siblings = $oid->getSiblings();
                    $uri = array_filter($siblings, function (ASN1\ASN1Object $value) {
                        return $value->getIdentifier()->getTagNumber() === 6;
                    });
                    $uri = array_pop($uri);
                    $uris[] = $uri->getBinaryContent();
                }
            }
        }
        return $uris;
    }

    /**
     * @return Name
     * @throws Exception
     */
    public function getIssuer(): Name
    {
        return new Name($this->getTBSCertificate()->getChildren()[3]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getValidNotBefore(): string
    {
        return (string)$this->getTBSCertificate()->getChildren()[4]->getChildren()[0];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getValidNotAfter(): string
    {
        return (string)$this->getTBSCertificate()->getChildren()[4]->getChildren()[1];
    }

    /**
     * @return Subject
     * @throws Exception
     */
    public function getSubject(): Subject
    {
        return new Subject($this->getTBSCertificate()->getChildren()[5]);
    }

    /**
     * @return string[]
     * @throws ASN1\Exception\ParserException
     */
    public function getCertPolicies(): array
    {
        $return = [];

        $policies = $this->getExtension(self::OID_EXTENSION_CERT_POLICIES);

        if (!is_null($policies)) {
            $return = array_map(function (Sequence $policy) {
                return (string)$policy->getChildren()[0];
            }, $policies->getChildren());
        }

        return $return;
    }

    /**
     * @return string[]
     * @throws ASN1\Exception\ParserException
     */
    public function getExtendedKeyUsage(): array
    {
        $return = [];

        $extKeyUsageSyntax = $this->getExtension(self::OID_EXTENSION_EXTENDED_KEY_USAGE);

        if (!is_null($extKeyUsageSyntax))
            $return = array_map('strval', $extKeyUsageSyntax->getChildren());

        return $return;
    }

    /**
     * @return bool
     * @throws ASN1\Exception\ParserException
     */
    public function isCa(): bool
    {
        $basicConstraints = $this->getExtension(self::OID_EXTENSION_BASIC_CONSTRAINTS);

        $isCa = 'false';

        if ($basicConstraints) {
            $isCa = $basicConstraints->getChildren()[0] ?? 'false';
        }

        return ((string)$isCa) === 'true';
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getSignatureAlgorithm(): AlgorithmIdentifier
    {
        return new AlgorithmIdentifier($this->object->getChildren()[1]);
    }

    /**
     * @return ASN1\ASN1ObjectInterface|BitString
     * @throws ASN1\Exception\ParserException
     */
    public function getSignature(): BitString
    {
        $binary = $this->object->getChildren()[2]->getBinary();

        return BitString::fromBinary($binary);
    }

    /**
     * @return PublicKey
     * @throws Exception
     */
    public function getPublicKey(): PublicKey
    {
        return new PublicKey($this->getTBSCertificate()->getChildren()[6]);
    }
}
