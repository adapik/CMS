<?php

namespace CMS;

use Adapik\CMS\SignerInfo;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

class SignerInfoTest extends TestCase
{
    const CADES_BES_SIGNATURE_VALUE = 'fb571c848212185cd46914df58f4f1085f1ffbb0c56e2313186626b446f5bcecc3170bbd9b869a492e42b5f55541f068b14fc291dc677847376f89292eca60de';

    const MESSAGE_DIGEST = '5ee4b6353be6190473db5d56d2b561c961d0748a74cd55c419e7af1557d126ab';

    const SIGNING_CERT_DIGEST = '3a3d172c013ef39bc3819f69f4e98ce9981eab9dc0df52b2d173b2815793a01d';

    public function testCreateFormSequence()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertInstanceOf(SignerInfo::class, $signerInfo);
    }

    public function testGetSignatureValue()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(self::CADES_BES_SIGNATURE_VALUE, $signerInfo->getSignatureValue());
    }

    public function testGetMessageDigest()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(self::MESSAGE_DIGEST, $signerInfo->getMessageDigest());
    }

    public function testGetSigningCertDigest()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(self::SIGNING_CERT_DIGEST, $signerInfo->getSigningCertDigest());
    }

    public function testDefineTypeBES()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(SignerInfo::TYPE_BES, $signerInfo->defineType());
    }

    public function testDefineTypeT()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoT());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(SignerInfo::TYPE_T, $signerInfo->defineType());
    }

    public function testDefineTypeXLongType1()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoXLongType1());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(SignerInfo::TYPE_X_LONG_TYPE1, $signerInfo->defineType());
    }

    public function testGetBinary()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame(base64_decode($this->getSignerInfoBES()), $signerInfo->getBinary());
    }

    public function testGetPublicKeyAlgorithm()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame('1.2.643.2.2.19', $signerInfo->getPublicKeyAlgorithm());
    }

    public function testGetDigestAlgorithm()
    {
        $sequence   = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertSame('1.2.643.2.2.9', $signerInfo->getDigestAlgorithm());
    }

    public function testCreateFromContent()
    {
        $sequence = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        $binary = $signerInfo->getBinary();
        $newSignerInfo = SignerInfo::createFromContent($binary);
        self::assertEquals($binary, $newSignerInfo->getBinary());

        $sequence = Sequence::fromFile($this->getSignerInfoXLongType1());
        $signerInfo = new SignerInfo($sequence);
        $binary = $signerInfo->getBinary();
        $newSignerInfo = SignerInfo::createFromContent($binary);
        self::assertEquals($binary, $newSignerInfo->getBinary());

        $sequence = Sequence::fromFile($this->getSignerInfoT());
        $signerInfo = new SignerInfo($sequence);
        $binary = $signerInfo->getBinary();
        $newSignerInfo = SignerInfo::createFromContent($binary);
        self::assertEquals($binary, $newSignerInfo->getBinary());
    }

    public function testGetSigningTime()
    {
        $sequence = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        self::assertNotNull($signerInfo->getSigningTime());

        $sequence = Sequence::fromFile($this->getSignerInfoXLongType1());
        $signerInfo = new SignerInfo($sequence);
        self::assertNotNull($signerInfo->getSigningTime());

        $sequence = Sequence::fromFile($this->getSignerInfoT());
        $signerInfo = new SignerInfo($sequence);
        self::assertNotNull($signerInfo->getSigningTime());
    }

    private function getSignerInfoBES()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_bes');
    }

    private function getSignerInfoXLongType1()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_xlongtype1');
    }

    private function getSignerInfoT()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_t');
    }
}
