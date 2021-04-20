<?php
/**
 * SingleResponseTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertID;
use Adapik\CMS\CertStatus;
use Adapik\CMS\SingleResponse;
use FG\ASN1\Universal\GeneralizedTime;
use PHPUnit\Framework\TestCase;

class SingleResponseTest extends TestCase
{
    public function testBasis()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        foreach ($basicOCSPResponse->getTbsResponseData()->getResponses() as $response) {
            self::assertInstanceOf(CertStatus::class, $response->getCertStatus());
            self::assertInstanceOf(CertID::class, $response->getCertID());
            self::assertInstanceOf(GeneralizedTime::class, $response->getThisUpdate());

            self::assertNull($response->getSingleExtensions());
            self::assertNull($response->getNextUpdate());

            $binary = $response->getBinary();
            $newSingleResponse = SingleResponse::createFromContent($binary);
            self::assertEquals($binary, $newSingleResponse->getBinary());

            return;
        }
    }
}
