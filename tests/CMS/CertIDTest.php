<?php
/**
 * CertIDTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertID;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;
use PHPUnit\Framework\TestCase;


class CertIDTest extends TestCase
{
    public function testBasis()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        foreach ($basicOCSPResponse->getTbsResponseData()->getResponses() as $response) {
            $certId = $response->getCertID();

            self::assertInstanceOf(AlgorithmIdentifier::class, $certId->getHashAlgorithm());
            self::assertInstanceOf(OctetString::class, $certId->getIssuerKeyHash());
            self::assertInstanceOf(OctetString::class, $certId->getIssuerNameHash());
            self::assertInstanceOf(Integer::class, $certId->getSerialNumber());

            $binary = $certId->getBinary();

            $newCertId = CertID::createFromContent($binary);

            self::assertEquals($binary, $newCertId->getBinary());

        }
    }
}
