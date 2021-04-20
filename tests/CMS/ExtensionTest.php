<?php
/**
 * ExtensionTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\Certificate;
use Adapik\CMS\Extension;
use FG\ASN1\ASN1Object;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\ObjectIdentifier;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase
{
    public function testBase()
    {
        $basicOCSPResponse = BasicOCSPResponse::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/BasicOCSPResponse.pem')));

        $extensions = $basicOCSPResponse->getTbsResponseData()->getExtensions();

        self::assertCount(2, $extensions);

        foreach ($extensions as $extension) {
            self::assertInstanceOf(ObjectIdentifier::class, $extension->getExtensionId());
            self::assertInstanceOf(ASN1Object::class, $extension->getExtensionValue());

            self::assertNull($extension->isCritical());

            $binary = $extension->getBinary();
            $newExtension = Extension::createFromContent($binary);
            self::assertEquals($binary, $newExtension->getBinary());
        }
    }

    public function testIsCritical()
    {
        $certificate = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_ca.crt')));
        foreach ($certificate->getTBSCertificate()->getExtensions() as $extension) {
            $critical = $extension->isCritical();
            if (!is_null($critical)) {
                self::assertInstanceOf(Boolean::class, $critical);
            }
        }
    }
}
