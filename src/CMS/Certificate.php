<?php

namespace Adapik\CMS;

use FG\ASN1;
use FG\ASN1\Object;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Certificate
 */
class Certificate
{
    const OID_EXTENSION_SUBJECT_KEY_ID        = '2.5.29.14';
    const OID_EXTENSION_AUTHORITY_KEY_ID      = '2.5.29.35';
    const OID_EXTENSION_AUTHORITY_INFO_ACCESS = '1.3.6.1.5.5.7.1.1';
    const OID_OCSP_URI                        = '1.3.6.1.5.5.7.48.1';

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
    /**
     * @return \FG\ASN1\Universal\Sequence
     */
    private function getTBSCertificate()
    {
        return $this->sequence->findChildrenByType(\FG\ASN1\Universal\Sequence::class)[0];
    }
    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->getTBSCertificate()->findChildrenByType(\FG\ASN1\Universal\Integer::class)[0]->getStringValue();
    }

    /**
     * @return Sequence
     */
    private function getExtensions()
    {
        $exTaggedObjects = $this->getTBSCertificate()->findChildrenByType(\FG\ASN1\ExplicitlyTaggedObject::class);
        $extensions      = array_filter($exTaggedObjects, function ($value) {
            return $value->identifier->getTagNumber() === 3;
        });

        return array_pop($extensions);
    }

    /**
     * @param string $oidString
     *
     * @return OctetString|null
     */
    private function getExtension(string $oidString)
    {
        $oid = $this->getExtensions()->findByOid($oidString);

        if ($oid) {
            $siblings = $oid[0]->getSiblings();

            return $siblings[0];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSubjectKeyIdentifier(): string
    {
        $content = $this->getExtension(self::OID_EXTENSION_SUBJECT_KEY_ID)->getBinaryContent();
        $body    = Object::fromBinary($content);

        return bin2hex($body->getBinaryContent());
    }

    /**
     * @return string
     */
    public function getAuthorityKeyIdentifier(): string
    {
        $content = $this->getExtension(self::OID_EXTENSION_AUTHORITY_KEY_ID)->getBinaryContent();
        $body    = Object::fromBinary($content);

        $children = $body->findChildrenByType(\FG\ASN1\ImplicitlyTaggedObject::class);
        $children = array_filter($children, function ($value) {
            return $value->identifier->getTagNumber() === 0;
        });

        return bin2hex($children[0]->getBinaryContent());
    }

    /**
     * @return string[]
     */
    public function getOcspUris(): array
    {
        $content = $this->getExtension(self::OID_EXTENSION_AUTHORITY_INFO_ACCESS)->getBinaryContent();
        $body    = Object::fromBinary($content);

        $oids = $body->findByOid(self::OID_OCSP_URI);

        $uris = [];

        if ($oids) {
            $uris = [];
            foreach ($oids as $oid) {
                $siblings = $oid->getSiblings();
                $uri      = array_filter($siblings, function ($value) {
                    return $value->identifier->getTagNumber() === 6;
                });
                $uri    = array_pop($uri);
                $uris[] = $uri->getBinaryContent();
            }
        }

        return $uris;
    }

    /**
     * Конструктор из бинарных данных
     *
     * @param $content
     *
     * @return Certificate
     */
    public static function createFromContent($content)
    {
        /** @var \FG\ASN1\Universal\Sequence $sequence */
        $sequence = ASN1\Object::fromFile($content);
        return new self($sequence);
    }
}
