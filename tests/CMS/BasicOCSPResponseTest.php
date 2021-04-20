<?php
/**
 * BasicOCSPResponseTest
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
use Adapik\CMS\Certificate;
use Adapik\CMS\ResponseData;
use FG\ASN1\Universal\BitString;
use PHPUnit\Framework\TestCase;

class BasicOCSPResponseTest extends TestCase
{
    public function testCreate()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        self::assertInstanceOf(BasicOCSPResponse::class, $basicOCSPResponse);

        $signatureAlgorithm = $basicOCSPResponse->getSignatureAlgorithm();
        $signature = $basicOCSPResponse->getSignature();
        $certs = $basicOCSPResponse->getCerts();
        $tbsResponseData = $basicOCSPResponse->getTbsResponseData();

        self::assertInstanceOf(AlgorithmIdentifier::class, $signatureAlgorithm);
        self::assertInstanceOf(BitString::class, $signature);
        self::assertCount(1, $certs);
        self::assertInstanceOf(Certificate::class, $certs[0]);
        self::assertInstanceOf(ResponseData::class, $tbsResponseData);
    }
}
