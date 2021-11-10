<?php

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CertificateInterface;
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Certificate
 *
 * @see     Maps\Certificate
 * @package Adapik\CMS
 */
class Certificate extends PEMBase implements CertificateInterface
{
    const OID_EXTENSION_SUBJECT_KEY_ID = '2.5.29.14';
    const OID_EXTENSION_BASIC_CONSTRAINTS = '2.5.29.19';
    const OID_EXTENSION_AUTHORITY_KEY_ID = '2.5.29.35';
    const OID_EXTENSION_AUTHORITY_INFO_ACCESS = '1.3.6.1.5.5.7.1.1';
    const OID_OCSP_URI = '1.3.6.1.5.5.7.48.1';
    const OID_EXTENSION_CERT_POLICIES = '2.5.29.32';
    const OID_EXTENSION_KEY_USAGE = '2.5.29.15';
    const OID_EXTENSION_EXTENDED_KEY_USAGE = '2.5.29.37';
    const PEM_HEADER = "BEGIN CERTIFICATE";
    const PEM_FOOTER = "END CERTIFICATE";

    /**
     * Конструктор из бинарных данных
     *
     * @param string $content
     * @return Certificate
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\Certificate::class, Sequence::class));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSerial(): string
    {
        return $this->getTBSCertificate()->getSerialNumber();
    }

    /**
     * @return TBSCertificate
     * @throws Exception
     */
    public function getTBSCertificate(): TBSCertificate
    {
        $binary = $this->object->findChildrenByType(Sequence::class)[0]->getBinary();

        return TBSCertificate::createFromContent($binary);
    }

    /**
     * @return string
     * @throws ASN1\Exception\ParserException
     */
    public function getSubjectKeyIdentifier(): string
    {
        $extension = $this->getExtension(self::OID_EXTENSION_SUBJECT_KEY_ID);

        return bin2hex($extension->getExtensionValue()->getBinaryContent());
    }

    /**
     * @param string $oidString
     *
     * @return ASN1\ASN1ObjectInterface|null
     * @throws ASN1\Exception\ParserException
     * @throws Exception
     */
    protected function getExtension(string $oidString): ?Extension
    {
        $return = null;

        foreach ($this->getTBSCertificate()->getExtensions() as $extension) {
            if ((string)$extension->getExtensionId() == $oidString) {
                $return = $extension;
                break;
            }
        }

        return $return;
    }

    /**
     * @return Extension[]
     * @throws Exception
     */
    public function getExtensions(): array
    {
        return $this->getTBSCertificate()->getExtensions();
    }

    /**
     * @return string|null
     * @throws ASN1\Exception\ParserException
     * @throws Exception
     */
    public function getAuthorityKeyIdentifier(): ?string
    {
        $return = null;

        $content = $this->_getExtension(self::OID_EXTENSION_AUTHORITY_KEY_ID);

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
     * @param string $oidString
     *
     * @return ASN1\ASN1ObjectInterface|null
     * @throws ASN1\Exception\ParserException
     * @throws Exception
     */
    protected function _getExtension(string $oidString): ?ASN1Object
    {
        $return = null;
        $oid = $this->_getExtensions()->findByOid($oidString);

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
    protected function _getExtensions(): ExplicitlyTaggedObject
    {
        $exTaggedObjects = $this->_getTBSCertificate()->findChildrenByType(ExplicitlyTaggedObject::class);
        /** @var ExplicitlyTaggedObject[] $extensions */
        $extensions = array_filter($exTaggedObjects, function (ASN1\ASN1Object $value) {
            return $value->getIdentifier()->getTagNumber() === 3;
        });

        return array_pop($extensions);
    }

    /**
     * @return Sequence
     * @throws Exception
     */
    protected function _getTBSCertificate(): Sequence
    {
        return $this->object->findChildrenByType(Sequence::class)[0];
    }

    /**
     * @return string[]
     * @throws ASN1\Exception\ParserException
     * @noinspection DuplicatedCode
     */
    public function getOcspUris(): array
    {
        $uris = [];

        $content = $this->_getExtension(self::OID_EXTENSION_AUTHORITY_INFO_ACCESS);

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
        return $this->getTBSCertificate()->getIssuer();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getValidNotBefore(): string
    {
        return $this->getTBSCertificate()->getValidNotBefore();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getValidNotAfter(): string
    {
        return $this->getTBSCertificate()->getValidNotAfter();
    }

    /**
     * @return Subject
     * @throws Exception
     */
    public function getSubject(): Subject
    {
        return $this->getTBSCertificate()->getSubject();
    }

    /**
     * @return string[]
     * @throws ASN1\Exception\ParserException
     */
    public function getCertPolicies(): array
    {
        $return = [];

        $policies = $this->_getExtension(self::OID_EXTENSION_CERT_POLICIES);

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

        $extKeyUsageSyntax = $this->_getExtension(self::OID_EXTENSION_EXTENDED_KEY_USAGE);

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
        $basicConstraints = $this->_getExtension(self::OID_EXTENSION_BASIC_CONSTRAINTS);

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
        return $this->getTBSCertificate()->getPublicKey();
    }
}
