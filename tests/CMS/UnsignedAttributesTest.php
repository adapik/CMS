<?php
/**
 * UnsignedAttributesTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\SignerInfo;
use Adapik\CMS\UnsignedAttributes;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

class UnsignedAttributesTest extends TestCase
{
    public function testSignerInfoBES()
    {
        $sequence = Sequence::fromFile($this->getSignerInfoBES());
        $signerInfo = new SignerInfo($sequence);
        $unsignedAttributes = $signerInfo->getUnsignedAttributes();

        self::assertNull($unsignedAttributes);
    }

    private function getSignerInfoBES()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_bes');
    }

    public function testSignerInfoXLongType1()
    {
        $sequence = Sequence::fromFile($this->getSignerInfoXLongType1());
        $signerInfo = new SignerInfo($sequence);
        $unsignedAttributes = $signerInfo->getUnsignedAttributes();

        self::assertNotNull($unsignedAttributes);

        $binary = $unsignedAttributes->getBinary();
        $newUnsignedAttributes = UnsignedAttributes::createFromContent($binary);
        self::assertEquals($binary, $newUnsignedAttributes->getBinary());

        foreach ($unsignedAttributes->getAttributes() as $attribute) {
            self::assertNotNull($unsignedAttributes->getByOid((string)$attribute->getIdentifier()));
        }
    }

    private function getSignerInfoXLongType1()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_xlongtype1');
    }

    public function testSignerInfoT()
    {
        $sequence = Sequence::fromFile($this->getSignerInfoT());
        $signerInfo = new SignerInfo($sequence);
        $unsignedAttributes = $signerInfo->getUnsignedAttributes();

        self::assertNotNull($unsignedAttributes);

        $binary = $unsignedAttributes->getBinary();
        $newUnsignedAttributes = UnsignedAttributes::createFromContent($binary);
        self::assertEquals($binary, $newUnsignedAttributes->getBinary());

        foreach ($unsignedAttributes->getAttributes() as $attribute) {
            self::assertNotNull($unsignedAttributes->getByOid((string)$attribute->getIdentifier()));
        }
    }

    private function getSignerInfoT()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_t');
    }
}
