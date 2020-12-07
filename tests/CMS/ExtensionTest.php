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
        $basicOCSPResponse = BasicOCSPResponse::createFromContent("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");

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
