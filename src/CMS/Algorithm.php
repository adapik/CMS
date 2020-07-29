<?php
/**
 * Algorithm
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;

class Algorithm
{
    const OID_MD2 = "1.2.840.113549.2.2";
    const OID_MD4 = "1.2.840.113549.2.4";
    const OID_MD5 = '1.2.840.113549.2.5';
    const OID_SHA1 = '1.3.14.3.2.26';
    const OID_SHA256 = '2.16.840.1.101.3.4.2.1';
    const OID_SHA384 = "2.16.840.1.101.3.4.2.2";
    const OID_SHA512 = "2.16.840.1.101.3.4.2.3";
    const OID_SHA224 = "2.16.840.1.101.3.4.2.4";
    const OID_RIPEMD160 = "1.3.36.3.2.1";
    const OID_RIPEMD128 = "1.3.36.3.2.2";
    const OID_RIPEMD256 = "1.3.36.3.2.3";
    const OID_GOST34_311_95 = "1.2.398.3.10.1.3.1";

    /**
     * @param string $oid
     *
     * @return string
     * @throws FormatException
     */
    public static function byOid(string $oid)
    {
        switch ($oid) {
            case self::OID_MD2:
                return 'md2';
            case self::OID_MD4:
                return 'md4';
            case self::OID_MD5:
                return 'md5';
            case self::OID_SHA1:
                return 'sha1';
            case self::OID_SHA256:
                return 'sha256';
            case self::OID_SHA384:
                return 'sha384';
            case self::OID_SHA512:
                return 'sha512';
            case self::OID_SHA224:
                return 'sha224';
            case self::OID_RIPEMD160:
                return 'ripemd160';
            case self::OID_RIPEMD128:
                return 'ripemd128';
            case self::OID_RIPEMD256:
                return 'ripemd256';
            case self::OID_GOST34_311_95:
                return 'gost';
            default:
                throw new FormatException('Unknown hash algorithm');
        }
    }

    /**
     * Converts string value to desired hash algorithm
     *
     * @param string $algorithmOID
     * @param string $value
     *
     * @param bool $raw
     * @return string
     * @throws FormatException
     */
    public static function hashValue(string $algorithmOID, string $value, bool $raw = true)
    {
        switch ($algorithmOID) {
            case self::OID_MD2:
                return hash('md2', $value, $raw);
            case self::OID_MD4:
                return hash('md4', $value, $raw);
            case self::OID_MD5:
                return hash('md5', $value, $raw);
            case self::OID_SHA1:
                return hash('sha1', $value, $raw);
            case self::OID_SHA256:
                return hash('sha256', $value, $raw);
            case self::OID_SHA384:
                return hash('sha384', $value, $raw);
            case self::OID_SHA512:
                return hash('sha512', $value, $raw);
            case self::OID_SHA224:
                return hash('sha224', $value, $raw);
            case self::OID_RIPEMD160:
                return hash('ripemd160', $value, $raw);
            case self::OID_RIPEMD128:
                return hash('ripemd128', $value, $raw);
            case self::OID_RIPEMD256:
                return hash('ripemd256', $value, $raw);
            case self::OID_GOST34_311_95:
                return hash('gost', $value, $raw);
            default:
                throw new FormatException('Unknown hash algorithm');
        }
    }
}
