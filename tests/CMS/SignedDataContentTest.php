<?php
/**
 * SignedDataContentTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\SignedData;
use Adapik\CMS\SignedDataContent;
use Adapik\CMS\SignerInfo;
use PHPUnit\Framework\TestCase;

class SignedDataContentTest extends TestCase
{
    public function testBase()
    {
        $signedData = SignedData::createFromContent(file_get_contents(__DIR__ . '/../fixtures/setOfUnsignedCMS.cms'));
        $signedDataContent = $signedData->getSignedDataContent();

        $binary = $signedDataContent->getBinary();
        $newSignedDataContent = SignedDataContent::createFromContent($binary);
        self::assertEquals($binary, $newSignedDataContent->getBinary());

        foreach ($signedDataContent->getSignerInfoSet() as $signerInfo) {
            self::assertNotNull($signedDataContent->getCertificateBySignerInfo($signerInfo));
        }

        $digestAlgorithmIdentifiers = $signedDataContent->getDigestAlgorithmIdentifiers();

        self::assertCount(1, $digestAlgorithmIdentifiers);

        foreach ($signedDataContent->getCertificateSet() as $certificate) {
            $signerInfo = $signedDataContent->getSignerInfoByCertificate($certificate);
            if (!is_null($signerInfo)) {
                self::assertInstanceOf(SignerInfo::class, $signerInfo);
            }
        }
    }
}
