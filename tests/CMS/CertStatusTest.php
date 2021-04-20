<?php
/**
 * CertStatusTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertStatus;
use PHPUnit\Framework\TestCase;

class CertStatusTest extends TestCase
{
    public function testBasis()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        foreach ($basicOCSPResponse->getTbsResponseData()->getResponses() as $response) {
            $status = $response->getCertStatus();

            self::assertTrue($status->isGood());
            self::assertFalse($status->isRevoked());
            self::assertFalse($status->isUnknown());

            $binary = $status->getBinary();
            $newCertStatus = CertStatus::createFromContent($binary);

            self::assertEquals($binary, $newCertStatus->getBinary());
        }
    }
}
