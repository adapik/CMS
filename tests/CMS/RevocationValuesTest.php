<?php
/**
 * RevocationValuesTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\RevocationValues;
use Adapik\CMS\SignedData;
use PHPUnit\Framework\TestCase;

class RevocationValuesTest extends TestCase
{
    public function testBasis()
    {
        $signedData = SignedData::createFromContent($this->getAttached());

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $revocationValues = $signerInfo->getUnsignedAttributes()->getRevocationValues();
            self::assertNull($revocationValues->getCertificateList());
            self::assertNotNull($revocationValues->getBasicOCSPResponse());

            $binary = $revocationValues->getBinary();
            $newRevocationValues = RevocationValues::createFromContent($revocationValues->getBinary());
            self::assertEquals($binary, $newRevocationValues->getBinary());

            return;
        }
    }

    private function getAttached()
    {
        return file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig');
    }
}
