<?php

namespace CMS;

use Adapik\CMS\Certificate;
use Adapik\CMS\SignedData;
use Adapik\CMS\SignerInfo;
use Adapik\CMS\Exception\FormatException;
use PHPUnit\Framework\TestCase;

class SignedDataTest extends TestCase
{
    public function testCreate()
    {
        $signedData = SignedData::createFromContent($this->getAttached());
        self::assertInstanceOf(SignedData::class, $signedData);
    }

    public function testCreateMalformed()
    {
        $this->expectException(FormatException::class);
        SignedData::createFromContent(base64_decode(123, true));
    }

    public function testGetSignerInfo()
    {
        $signedData = SignedData::createFromContent($this->getAttached());
        $signerInfo = $signedData->getSignerInfo();
        self::assertCount(1, $signerInfo);
        self::assertContainsOnlyInstancesOf(SignerInfo::class, $signerInfo);
    }

    public function testExtractCertificates()
    {
        $signedData = SignedData::createFromContent($this->getAttached());
        $certs      = $signedData->extractCertificates();
        self::assertCount(2, $certs);
        self::assertContainsOnlyInstancesOf(Certificate::class, $certs);
    }

    public function testHasData()
    {
        $signedData = SignedData::createFromContent($this->getAttached());
        self::assertTrue($signedData->hasData());

        $signedData = SignedData::createFromContent($this->getDetached());
        self::assertFalse($signedData->hasData());
    }

    public function testGetData()
    {
        $signedData = SignedData::createFromContent($this->getAttached());
        self::assertSame('1', $signedData->getData());

        $signedData = SignedData::createFromContent($this->getDetached());
        self::assertNull($signedData->getData());
    }

    public function testGetBinary()
    {
        $signedData = SignedData::createFromContent($this->getDetached());
        self::assertSame(base64_decode($this->getDetached()), $signedData->getBinary());
    }

    private function getAttached()
    {
        return file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig');
    }

    private function getDetached()
    {
        return file_get_contents(__DIR__ . '/../fixtures/cms_detached_cert.sig');
    }
}
