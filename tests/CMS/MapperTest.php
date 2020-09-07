<?php

namespace Adapik\Test\CMS;

use Adapik\CMS\Maps\Certificate;
use Adapik\CMS\Maps\SignedData;
use Adapik\CMS\Maps\SignerInfo;
use FG\ASN1\Mapper\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * Test for Maps
 */
class MapperTest extends TestCase
{
    public function testMapCert()
    {
        $map          = Certificate::MAP;
        $userCert     = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt'));
        $sequence     = \FG\ASN1\ASN1Object::fromFile($userCert);
        $mappedObject = (new Mapper())->map($sequence, $map);
        $this->assertNotNull($mappedObject);

        $this->assertArrayHasKey('tbsCertificate', $mappedObject);
        $this->assertArrayHasKey('signatureAlgorithm', $mappedObject);
        $this->assertArrayHasKey('signature', $mappedObject);

        $tbsCertificate = $mappedObject['tbsCertificate'];

        $this->assertArrayHasKey('version', $tbsCertificate);
        $this->assertArrayHasKey('serialNumber', $tbsCertificate);
        $this->assertArrayHasKey('signature', $tbsCertificate);
        $this->assertArrayHasKey('issuer', $tbsCertificate);
        $this->assertArrayHasKey('validity', $tbsCertificate);
        $this->assertArrayHasKey('subject', $tbsCertificate);
        $this->assertArrayHasKey('subjectPublicKeyInfo', $tbsCertificate);
        $this->assertArrayHasKey('extensions', $tbsCertificate);
    }

    public function testMapSignerInfo()
    {
        $map          = SignerInfo::MAP;
        $signerInfo   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_bes'));
        $sequence     = \FG\ASN1\ASN1Object::fromFile($signerInfo);
        $mappedObject = (new Mapper())->map($sequence, $map);
        $this->assertNotNull($mappedObject);

        $this->assertArrayHasKey('version', $mappedObject);
        $this->assertArrayHasKey('signerIdentifier', $mappedObject);
        $this->assertArrayHasKey('digestAlgorithm', $mappedObject);
        $this->assertArrayHasKey('signedAttrs', $mappedObject);
        $this->assertArrayHasKey('signatureAlgorithm', $mappedObject);
        $this->assertArrayHasKey('signature', $mappedObject);
    }

    public function testMapSignedData()
    {
        $map          = SignedData::MAP;
        $signerInfo   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig'));
        $sequence     = \FG\ASN1\ASN1Object::fromFile($signerInfo);
        $mappedObject = (new Mapper())->map($sequence, $map);
        $this->assertNotNull($mappedObject);

        $this->assertArrayHasKey('contentType', $mappedObject);
        $this->assertArrayHasKey('content', $mappedObject);

        $content = $mappedObject['content'];

        $this->assertArrayHasKey('version', $content);
        $this->assertArrayHasKey('digestAlgorithms', $content);
        $this->assertArrayHasKey('encapsulatedContentInfo', $content);
        $this->assertArrayHasKey('certificates', $content);
        $this->assertCount(2, $content['certificates']);
        $this->assertArrayHasKey('signerInfos', $content);
        $this->assertCount(1, $content['signerInfos']);
    }
}