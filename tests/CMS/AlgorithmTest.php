<?php
/**
 * AlgorithmTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Exception\FormatException;
use PHPUnit\Framework\TestCase;

class AlgorithmTest extends TestCase
{
    /**
     * @throws FormatException
     */
    public function testByOid()
    {
        self::assertEquals("md2", Algorithm::byOid(Algorithm::OID_MD2));
        self::assertEquals("md4", Algorithm::byOid(Algorithm::OID_MD4));
        self::assertEquals("md5", Algorithm::byOid(Algorithm::OID_MD5));
        self::assertEquals("sha1", Algorithm::byOid(Algorithm::OID_SHA1));
        self::assertEquals("sha256", Algorithm::byOid(Algorithm::OID_SHA256));
        self::assertEquals("sha384", Algorithm::byOid(Algorithm::OID_SHA384));
        self::assertEquals("sha512", Algorithm::byOid(Algorithm::OID_SHA512));
        self::assertEquals("sha224", Algorithm::byOid(Algorithm::OID_SHA224));
        self::assertEquals("ripemd160", Algorithm::byOid(Algorithm::OID_RIPEMD160));
        self::assertEquals("ripemd128", Algorithm::byOid(Algorithm::OID_RIPEMD128));
        self::assertEquals("ripemd256", Algorithm::byOid(Algorithm::OID_RIPEMD256));
        self::assertEquals("gost", Algorithm::byOid(Algorithm::OID_GOST34_311_95));
        self::assertEquals("gost", Algorithm::byOid(Algorithm::OID_GOST34_11_94));

        self::expectException(FormatException::class);
        Algorithm::byOid("1.1.1.1.1");
    }

    public function testHashValue()
    {
        self::assertEquals("dd34716876364a02d0195e2fb9ae2d1b", Algorithm::hashValue(Algorithm::OID_MD2, 'test', false));
        self::assertEquals("db346d691d7acc4dc2625db19f9e3f52", Algorithm::hashValue(Algorithm::OID_MD4, 'test', false));
        self::assertEquals("098f6bcd4621d373cade4e832627b4f6", Algorithm::hashValue(Algorithm::OID_MD5, 'test', false));
        self::assertEquals("a94a8fe5ccb19ba61c4c0873d391e987982fbbd3", Algorithm::hashValue(Algorithm::OID_SHA1, 'test', false));
        self::assertEquals("9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08", Algorithm::hashValue(Algorithm::OID_SHA256, 'test', false));
        self::assertEquals("768412320f7b0aa5812fce428dc4706b3cae50e02a64caa16a782249bfe8efc4b7ef1ccb126255d196047dfedf17a0a9", Algorithm::hashValue(Algorithm::OID_SHA384, 'test', false));
        self::assertEquals("ee26b0dd4af7e749aa1a8ee3c10ae9923f618980772e473f8819a5d4940e0db27ac185f8a0e1d5f84f88bc887fd67b143732c304cc5fa9ad8e6f57f50028a8ff", Algorithm::hashValue(Algorithm::OID_SHA512, 'test', false));
        self::assertEquals("90a3ed9e32b2aaf4c61c410eb925426119e1a9dc53d4286ade99a809", Algorithm::hashValue(Algorithm::OID_SHA224, 'test', false));
        self::assertEquals("5e52fee47e6b070565f74372468cdc699de89107", Algorithm::hashValue(Algorithm::OID_RIPEMD160, 'test', false));
        self::assertEquals("f1abb5083c9ff8a9dbbca9cd2b11fead", Algorithm::hashValue(Algorithm::OID_RIPEMD128, 'test', false));
        self::assertEquals("fe0289110d07daeee9d9500e14c57787d9083f6ba10e6bcb256f86bb4fe7b981", Algorithm::hashValue(Algorithm::OID_RIPEMD256, 'test', false));
        self::assertEquals("a6e1acdd0cc7e00d02b90bccb2e21892289d1e93f622b8760cb0e076def1f42b", Algorithm::hashValue(Algorithm::OID_GOST34_311_95, 'test', false));

        self::expectException(FormatException::class);
        Algorithm::hashValue("1.1.1.1.1", 'test', true);
    }
}
