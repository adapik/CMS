<?php
/**
 * NameTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\Certificate;
use Adapik\CMS\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testBase()
    {
        $certificate = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt')));
        $name = $certificate->getTBSCertificate()->getIssuer();

        $array = $name->toArray();
        $string = $name->__toString();

        self::assertCount(5, $array);
        self::assertEquals("2.5.4.6: FR; 2.5.4.8: Paris; 2.5.4.7: Paris; 2.5.4.10: Gandi; 2.5.4.3: Gandi Standard SSL CA 2", $string);

        $binary = $name->getBinary();
        $newName = Name::createFromContent($binary);

        self::assertEquals($binary, $newName->getBinary());
    }
}
