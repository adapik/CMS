<?php

namespace Adapik\Test\CMS;

use Adapik\CMS\Certificate;
use PHPUnit\Framework\TestCase;

/**
 * Test Certificate class
 */
class CertificateTest extends TestCase
{
    const CERT_SERIAL         = '0191475573341482871230183340876003493987';
    const CERT_SUBJECT_KEY_ID = '005ecbf504b0d74b3517cc4ebc1dc73e3731d237';
    const CERT_ISSUER_KEY_ID  = 'b390a7d8c9af4ecd613c9f7cad5d7f41fd6930ea';
    const CERT_OCSP_URI       = 'http://ocsp.usertrust.com';

    /**
     * Binary content of Certificate file
     *
     * @var string
     */
    private $content;

    public function setUp()
    {
        $this->content = base64_decode(file_get_contents(__DIR__ . '/../fixtures/phpnet.crt'));
    }

    public function testParseCert()
    {
        $cert = Certificate::createFromContent($this->content);
        $this->assertInstanceOf(Certificate::class, $cert);
    }

    public function testGetSerial()
    {
        $cert = Certificate::createFromContent($this->content);
        $this->assertEquals(self::CERT_SERIAL, $cert->getSerial());
    }

    public function testGetSubjectKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->content);
        $this->assertEquals(self::CERT_SUBJECT_KEY_ID, $cert->getSubjectKeyIdentifier());
    }

    public function testGetAuthorityKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->content);
        $this->assertEquals(self::CERT_ISSUER_KEY_ID, $cert->getAuthorityKeyIdentifier());
    }

    public function testGetOcspUris()
    {
        $cert = Certificate::createFromContent($this->content);
        $this->assertEquals([self::CERT_OCSP_URI], $cert->getOcspUris());
    }
}
