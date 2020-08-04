<?php
/**
 * CMSBase
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
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
     * @param string $content
     * @param string $mapperClass
     * @param string $objectClass
     * @return ASN1ObjectInterface
     * @throws FormatException
     */
    final protected static function makeFromContent(string $content, string $mapperClass, string $objectClass)
    {
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
    private static function className(string $classname)
    {
        return (substr($classname, strrpos($classname, '\\') + 1));
    }

    /**
     * @return string
     */
    final public function getBinary()
    {
        return $this->object->getBinary();
    }

    /**
     * @return string
     */
    final public function getBinaryContent()
    {
        return $this->object->getBinaryContent();
    }
}
