<?php
/**
 * UnsignedAttributeTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\SignerInfo;
use Adapik\CMS\UnsignedAttribute;
use Exception;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

class UnsignedAttributeTest extends TestCase
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

        $attributes = $unsignedAttributes->getAttributes();
        foreach ($attributes as $attribute) {
            self::assertNotNull($attribute->getValue());
            self::assertNotNull($attribute->getIdentifier());
        }

        self::expectException(Exception::class);
        UnsignedAttribute::createFromContent("");
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

        $attributes = $unsignedAttributes->getAttributes();

        foreach ($attributes as $attribute) {
            self::assertNotNull($attribute->getValue());
            self::assertNotNull($attribute->getIdentifier());
        }

        self::expectException(Exception::class);
        UnsignedAttribute::createFromContent("");
    }

    private function getSignerInfoT()
    {
        return file_get_contents(__DIR__ . '/../fixtures/signer_info_cades_t');
    }
}
