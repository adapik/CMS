<?php
/**
 * ASNBase
 *
 * @package      Adapik\CMS
 * @copyright    Copyright © Real Time Engineering, LLP - All Rights Reserved
 * @license      Proprietary and confidential
 * Unauthorized copying or using of this file, via any medium is strictly prohibited.
 * Content can not be copied and/or distributed without the express permission of Real Time Engineering, LLP
 *
 * @author       Written by Nurlan Mukhanov <nmukhanov@mp.kz>, июль 2020
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1Object;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Mapper\Mapper;

/**
 * Class CMSBase
 * @package Adapik\CMS
 */
abstract class CMSBase implements CMSInterface
{
    /** @var ASN1Object $object */
    protected $object;

    /**
     * CMSBase constructor.
     *
     * @param ASN1ObjectInterface $object
     */
    public function __construct(ASN1ObjectInterface $object)
    {
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->object->getBinary();
    }

    /**
     * @return string
     */
    public function getBinaryContent()
    {
        return $this->object->getBinaryContent();
    }

    /**
     * @param string $content
     * @param string $mapperClass
     * @param string $objectClass
     * @return ASN1Object
     * @throws FormatException
     */
    protected static function makeFromContent(string $content, string $mapperClass, string $objectClass) {
        $object = ASN1Object::fromFile($content);

        if (!$object instanceof $objectClass) {
            throw new FormatException(self::className($mapperClass) . ' must be type of ' . self::className($objectClass));
        }

        $map = (new Mapper())->map($object, $mapperClass::MAP);

        if ($map === null) {
            throw new FormatException(self::className($mapperClass) . ' invalid format');
        }

        return $object;
    }

    /**
     * @param string $classname
     * @return false|string
     */
    private static function className(string $classname) {
        return (substr($classname, strrpos($classname, '\\') + 1));
    }
}
