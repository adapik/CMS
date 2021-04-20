<?php
/**
 * IssuerAndSerialNumberTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\IssuerAndSerialNumber;
use Adapik\CMS\Name;
use Adapik\CMS\SignedData;
use PHPUnit\Framework\TestCase;

class IssuerAndSerialNumberTest extends TestCase
{
    public function testBase()
    {
        $signedData = SignedData::createFromContent(file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig'));
        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $issuerAndSerialNumber = $signerInfo->getIssuerAndSerialNumber();

            self::assertInstanceOf(Name::class, $issuerAndSerialNumber->getIssuer());
            self::assertEquals("24327317643217621957284221647236905054", $issuerAndSerialNumber->getSerialNumber());

            $binary = $issuerAndSerialNumber->getBinary();
            $newIssuerAndSerialNumber = IssuerAndSerialNumber::createFromContent($binary);
            self::assertEquals($binary, $newIssuerAndSerialNumber->getBinary());
        }
    }
}
