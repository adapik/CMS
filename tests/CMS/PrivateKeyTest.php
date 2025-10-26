<?php
/**
 * PrivateKeyTest
 *
 * @author    Alexander Danilov <adapik@yandex.ru>
 * @copyright 2025 Alexander Danilov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace Adapik\Test\CMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\PEMConverter;
use Adapik\CMS\PrivateKey;
use Exception;
use FG\ASN1\Universal\OctetString;
use PHPUnit\Framework\TestCase;

class PrivateKeyTest extends TestCase
{
    public function testBase()
    {
        $privateKey = PrivateKey::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/private_key.key')));

        self::assertInstanceOf(OctetString::class, $privateKey->getKey());
        self::assertInstanceOf(AlgorithmIdentifier::class, $privateKey->getKeyAlgorithm());
        self::assertIsInt($privateKey->getVersion());
        self::assertEquals(0, $privateKey->getVersion());

        $binary = $privateKey->getBinary();
        $newPrivateKey = PrivateKey::createFromContent($binary);

        self::assertEquals($binary, $newPrivateKey->getBinary());
    }

    /**
     * @throws FormatException
     * @throws Exception
     */
    public function testGetPEM()
    {
        $privateKey = PrivateKey::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/private_key.key')));

        $pem = PEMConverter::toPEM($privateKey);
        preg_match('/-+([^-]+)-+(.*?)-+([^-]+)-+/ms', $pem, $matches);
        self::assertSame($privateKey->getPEMHeader(), $matches[1]);
        self::assertSame($privateKey->getPEMFooter(), $matches[3]);
        self::assertSame($privateKey->getBase64(false), str_replace(["\r", "\n", "\r\n"], "", $matches[2]));
    }

    public function testKeyAlgorithm()
    {
        $privateKey = PrivateKey::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/private_key.key')));
        $algorithm = $privateKey->getKeyAlgorithm();

        self::assertInstanceOf(AlgorithmIdentifier::class, $algorithm);
        // RSA encryption OID is 1.2.840.113549.1.1.1
        self::assertStringContainsString('1.2.840.113549.1.1.1', $algorithm->getAlgorithmOid());
    }
}
