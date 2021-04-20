<?php
/**
 * CertificateListTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\CertificateList;
use Adapik\CMS\SignedData;
use Adapik\CMS\TBSCertList;
use FG\ASN1\Universal\BitString;
use PHPUnit\Framework\TestCase;

class CertificateListTest extends TestCase
{
    public function testBasis()
    {
        $signedData = SignedData::createFromContent($this->getAttached());

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $revocationValues = $signerInfo->getUnsignedAttributes()->getRevocationValues();
            $certificateList = $revocationValues->getCertificateList();

            if (!is_null($certificateList)) {
                self::assertInstanceOf(AlgorithmIdentifier::class, $certificateList->getSignatureAlgorithm());
                self::assertInstanceOf(BitString::class, $certificateList->getSignature());

                $TBSCertList = $certificateList->getTBSCertList();
                self::assertInstanceOf(TBSCertList::class, $TBSCertList);

                $binary = $TBSCertList->getBinary();
                $newTBSCertList = TBSCertList::createFromContent($binary);
                self::assertEquals($binary, $newTBSCertList->getBinary());

                $binary = $certificateList->getBinary();
                $newCertificateList = CertificateList::createFromContent($binary);
                self::assertEquals($binary, $newCertificateList->getBinary());

            }
        }
    }

    private function getAttached()
    {
        return file_get_contents(__DIR__ . '/../fixtures/setOfUnsignedCMS.cms');
    }

}
