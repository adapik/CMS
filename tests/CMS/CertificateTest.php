<?php

namespace Adapik\Test\CMS;

use Adapik\CMS\Certificate;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\PublicKey;
use FG\ASN1\Universal\BitString;
use PHPUnit\Framework\TestCase;

/**
 * Test Certificate class
 */
class CertificateTest extends TestCase
{
    const CERT_SERIAL         = '191475573341482871230183340876003493987';
    const CERT_SUBJECT_KEY_ID = '005ecbf504b0d74b3517cc4ebc1dc73e3731d237';
    const CERT_ISSUER_KEY_ID  = 'b390a7d8c9af4ecd613c9f7cad5d7f41fd6930ea';
    const CERT_OCSP_URI       = 'http://ocsp.usertrust.com';

    /**
     * Binary content of User Certificate file
     *
     * @var string
     */
    private $userCert;

    /**
     * Binary content of CA Certificate file
     *
     * @var string
     */
    private $caCert;

    protected function setUp(): void
    {
        $this->userCert = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt'));
        $this->caCert   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_ca.crt'));
    }

    protected function tearDown(): void
    {
        $this->userCert = null;
        $this->caCert   = null;
    }

    public function testCreate()
    {
        $signedData = Certificate::createFromContent($this->userCert);
        self::assertInstanceOf(Certificate::class, $signedData);
    }

    public function testCreateMalformed()
    {
        $this->expectException(FormatException::class);
        Certificate::createFromContent(base64_decode(123, true));
    }

    public function testParseCert()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertInstanceOf(Certificate::class, $cert);
    }

    public function testGetSerial()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals(self::CERT_SERIAL, $cert->getSerial());
    }

    public function testGetSubjectKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals(self::CERT_SUBJECT_KEY_ID, $cert->getSubjectKeyIdentifier());
    }

    public function testGetAuthorityKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals(self::CERT_ISSUER_KEY_ID, $cert->getAuthorityKeyIdentifier());
    }

    public function testGetOcspUris()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals([self::CERT_OCSP_URI], $cert->getOcspUris());
    }

    public function testGetIssuer()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals('2.5.4.6: FR; 2.5.4.8: Paris; 2.5.4.7: Paris; 2.5.4.10: Gandi; 2.5.4.3: Gandi Standard SSL CA 2', (string) $cert->getIssuer());
    }

    public function testGetSubject()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals('2.5.4.11: Domain Control Validated; 2.5.4.11: Gandi Standard Wildcard SSL; 2.5.4.3: *.php.net', (string) $cert->getSubject());
    }

    public function testGetValidNotBefore()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals('2016-06-02T00:00:00+00:00', (string) $cert->getValidNotBefore());
    }

    public function testGetValidNotAfter()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertEquals('2019-06-02T23:59:59+00:00', (string) $cert->getValidNotAfter());
    }

    public function testIsCAFalse()
    {
        $cert = Certificate::createFromContent($this->userCert);
        self::assertFalse($cert->isCa());
    }

    public function testIsCATrue()
    {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertTrue($cert->isCa());
    }

    public function testGetPolicies()
    {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertSame([
            '1.3.6.1.4.1.6449.1.2.2.26',
            '2.23.140.1.2.1',
        ], $cert->getCertPolicies());
    }

    public function testGetExtendedKeyUsage()
    {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertSame([
            '1.3.6.1.5.5.7.3.1',
            '1.3.6.1.5.5.7.3.2',
        ], $cert->getExtendedKeyUsage());
    }

    public function testGetBinary()
    {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertSame($this->caCert, $cert->getBinary());
    }
    
    public function testSetPublicKey() {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertInstanceOf(PublicKey::class, $cert->getPublicKey());
    }

    public function testGetSignature() {
        $cert = Certificate::createFromContent($this->caCert);
        self::assertInstanceOf(BitString::class, $cert->getSignature());
    }

    public function testGetExtensions() {
        $cert = Certificate::createFromContent($this->userCert);
        $extensions = $cert->getExtensions();

        self::assertCount(9,$extensions);
    }

    /**
     * @throws FormatException
     */
    public function testGetPEM() {
        $certificate = Certificate::createFromContent($this->userCert);
        $pem = $certificate->getPEM();
        preg_match('/-+([^-]+)-+(.*?)-+([^-]+)-+/ms', $pem, $matches);
        self::assertSame(Certificate::PEM_HEADER, $matches[1]);
        self::assertSame(Certificate::PEM_FOOTER, $matches[3]);
        self::assertSame($certificate->getBase64(false), str_replace(["\r", "\n", "\r\n"], "", $matches[2]));
    }
}
