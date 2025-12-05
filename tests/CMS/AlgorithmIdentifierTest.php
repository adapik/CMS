<?php
/**
 * AlgorithmIdentifierTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\SignedData;
use FG\ASN1\ASN1Object;
use PHPUnit\Framework\TestCase;

class AlgorithmIdentifierTest extends TestCase
{
    /**
     * @throws FormatException
     */
    public function testBase()
    {
        $binary = base64_decode(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));
        $signedData = SignedData::createFromContent($binary);

        foreach ($signedData->getSignedDataContent()->getCertificateSet() as $certificate) {
            $signatureAlgorithm = $certificate->getSignatureAlgorithm();

            self::assertInstanceOf(AlgorithmIdentifier::class, $signatureAlgorithm);
            self::assertEquals("1.2.840.113549.1.1.11", $signatureAlgorithm->getAlgorithmOid());

            $binary = $signatureAlgorithm->getBinary();

            $newSA = AlgorithmIdentifier::createFromContent($binary);

            self::assertEquals($binary, $newSA->getBinary());
        }
    }

    /**
     * @throws FormatException
     */
    public function testGetParameters()
    {
        $binary = base64_decode(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));
        $signedData = SignedData::createFromContent($binary);

        foreach ($signedData->getSignedDataContent()->getCertificateSet() as $certificate) {
            $signatureAlgorithm = $certificate->getSignatureAlgorithm();

            $parameters = $signatureAlgorithm->getParameters();

            self::assertInstanceOf(ASN1Object::class, $parameters);
        }
    }
}
