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
        self::assertNotNull($mappedObject);

        self::assertArrayHasKey('tbsCertificate', $mappedObject);
        self::assertArrayHasKey('signatureAlgorithm', $mappedObject);
        self::assertArrayHasKey('signature', $mappedObject);

        $tbsCertificate = $mappedObject['tbsCertificate'];

        self::assertArrayHasKey('version', $tbsCertificate);
        self::assertArrayHasKey('serialNumber', $tbsCertificate);
        self::assertArrayHasKey('signature', $tbsCertificate);
        self::assertArrayHasKey('issuer', $tbsCertificate);
        self::assertArrayHasKey('validity', $tbsCertificate);
        self::assertArrayHasKey('subject', $tbsCertificate);
        self::assertArrayHasKey('subjectPublicKeyInfo', $tbsCertificate);
        self::assertArrayHasKey('extensions', $tbsCertificate);
    }

    public function testMapSignerInfo()
    {
        $map          = SignerInfo::MAP;
        $signerInfo   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_bes'));
        $sequence     = \FG\ASN1\ASN1Object::fromFile($signerInfo);
        $mappedObject = (new Mapper())->map($sequence, $map);
        self::assertNotNull($mappedObject);

        self::assertArrayHasKey('version', $mappedObject);
        self::assertArrayHasKey('signerIdentifier', $mappedObject);
        self::assertArrayHasKey('digestAlgorithm', $mappedObject);
        self::assertArrayHasKey('signedAttrs', $mappedObject);
        self::assertArrayHasKey('signatureAlgorithm', $mappedObject);
        self::assertArrayHasKey('signature', $mappedObject);
    }

    public function testMapSignedData()
    {
        $map          = SignedData::MAP;
        $signerInfo   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig'));
        $sequence     = \FG\ASN1\ASN1Object::fromFile($signerInfo);
        $mappedObject = (new Mapper())->map($sequence, $map);
        self::assertNotNull($mappedObject);

        self::assertArrayHasKey('contentType', $mappedObject);
        self::assertArrayHasKey('content', $mappedObject);

        $content = $mappedObject['content'];

        self::assertArrayHasKey('version', $content);
        self::assertArrayHasKey('digestAlgorithms', $content);
        self::assertArrayHasKey('encapsulatedContentInfo', $content);
        self::assertArrayHasKey('certificates', $content);
        self::assertCount(2, $content['certificates']);
        self::assertArrayHasKey('signerInfos', $content);
        self::assertCount(1, $content['signerInfos']);
    }
}
