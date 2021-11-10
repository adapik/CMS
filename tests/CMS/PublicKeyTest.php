<?php
/**
 * PublicKeyTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\Test\CMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Certificate;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\PublicKey;
use Exception;
use FG\ASN1\Universal\BitString;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    public function testBase()
    {
        $certificate = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt')));
        $publicKey = $certificate->getPublicKey();

        self::assertInstanceOf(BitString::class, $publicKey->getKey());
        self::assertInstanceOf(AlgorithmIdentifier::class, $publicKey->getKeyAlgorithm());

        $binary = $publicKey->getBinary();
        $newPublicKey = PublicKey::createFromContent($binary);

        self::assertEquals($binary, $newPublicKey->getBinary());
    }

    /**
     * @throws FormatException
     * @throws Exception
     */
    public function testGetPEM() {
        $certificate = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt')));
        $publicKey = $certificate->getPublicKey();

        $pem = $publicKey->getPEM();
        preg_match('/-+([^-]+)-+(.*?)-+([^-]+)-+/ms', $pem, $matches);
        self::assertSame(PublicKey::PEM_HEADER, $matches[1]);
        self::assertSame(PublicKey::PEM_FOOTER, $matches[3]);
        self::assertSame($publicKey->getBase64(false), str_replace(["\r", "\n", "\r\n"], "", $matches[2]));
    }
}
