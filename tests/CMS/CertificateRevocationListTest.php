<?php

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\CertificateRevocationList;
use PHPUnit\Framework\TestCase;

/**
 * Test CertificateRevocationList class
 */
class CertificateRevocationListTest extends TestCase
{
    /**
     * @var string
     */
    private $crl;

    protected function setUp()
    {
        $this->crl = base64_decode(file_get_contents(__DIR__ . '/../fixtures/crl.crl'));
    }

    protected function tearDown()
    {
        $this->crl = null;
    }

    public function testCreateFromContent()
    {
        $crl = CertificateRevocationList::createFromContent($this->crl);
        $this->assertInstanceOf(CertificateRevocationList::class, $crl);
    }

    public function testGetIssuer()
    {
        $crl = CertificateRevocationList::createFromContent($this->crl);
        $this->assertSame(
            '1.2.840.113549.1.9.1: dit@minsvyaz.ru; 2.5.4.6: RU; 2.5.4.8: 77 г. Москва; 2.5.4.7: Москва; 2.5.4.9: 125375 г. Москва, ул. Тверская, д. 7; 2.5.4.10: Минкомсвязь России; 1.2.643.100.1: 1047702026701; 1.2.643.3.131.1.1: 007710474375; 2.5.4.3: Головной удостоверяющий центр',
            (string) $crl->getIssuer()
        );
    }

    public function testGetNextUpdate()
    {
        $crl = CertificateRevocationList::createFromContent($this->crl);
        $this->assertSame('2018-04-06T08:33:56+00:00', $crl->getNextUpdate());
    }

    public function testGetThisUpdate()
    {
        $crl = CertificateRevocationList::createFromContent($this->crl);
        $this->assertSame('2018-03-07T08:33:56+00:00', $crl->getThisUpdate());
    }

    public function testGetSerialNumbers()
    {
        $crl = CertificateRevocationList::createFromContent($this->crl);
        $this->assertCount(27, $crl->getSerialNumbers());
    }
}
