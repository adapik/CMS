<?php
/**
 * ResponseDataTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\ResponseData;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Universal\GeneralizedTime;
use PHPUnit\Framework\TestCase;

class ResponseDataTest extends TestCase
{
    public function testBasis()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        $responseData = $basicOCSPResponse->getTbsResponseData();

        $extensions = $responseData->getExtensions();
        $responses = $responseData->getResponses();
        $producedAt = $responseData->getProducedAt();
        $responderID = $responseData->getResponderID();

        self::assertCount(2, $extensions);
        self::assertCount(1, $responses);
        self::assertInstanceOf(GeneralizedTime::class, $producedAt);
        self::assertEquals("2020-07-05T16:07:38+00:00", $producedAt->__toString());
        self::assertInstanceOf(ASN1ObjectInterface::class, $responderID);

        $binary = $responseData->getBinary();
        $newResponseData = ResponseData::createFromContent($binary);
        self::assertEquals($binary, $newResponseData->getBinary());
    }
}
