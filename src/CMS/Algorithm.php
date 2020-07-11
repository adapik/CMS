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

    /**
     * Converts string value to desired hash algorithm
     *
     * @param string $algorithmOID
     * @param string $value
     *
     * @return string
     * @throws FormatException
     */
    public static function hashValue(string $algorithmOID, string $value)
    {
        switch ($algorithmOID) {
            case self::OID_MD2:
                return hash('md2', $value, true);
            case self::OID_MD4:
                return hash('md4', $value, true);
            case self::OID_MD5:
                return hash('md5', $value, true);
            case self::OID_SHA1:
                return hash('sha1', $value, true);
            case self::OID_SHA256:
                return hash('sha256', $value, true);
            case self::OID_SHA384:
                return hash('sha384', $value, true);
            case self::OID_SHA512:
                return hash('sha512', $value, true);
            case self::OID_SHA224:
                return hash('sha224', $value, true);
            case self::OID_RIPEMD160:
                return hash('ripemd160', $value, true);
            case self::OID_RIPEMD128:
                return hash('ripemd128', $value, true);
            case self::OID_RIPEMD256:
                return hash('ripemd256', $value, true);
            default:
                throw new FormatException('Unknown hash algorithm');
        }
    }
}
