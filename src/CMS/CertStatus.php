<?php
/**
 * CertStatus
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\AbstractTaggedObject;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;

/**
 * Class CertStatus
 *
 * @see     Maps\CertStatus
 * @package Adapik\CMS
 */
class CertStatus
{
    /**
     * @var ImplicitlyTaggedObject|ExplicitlyTaggedObject
     */
    private $object;

    /**
     * In case of successful check it is ExplicitlyTaggedObject, otherwise ImplicitlyTaggedObject
     * ExplicitlyTaggedObject constructor.
     *
     * @param AbstractTaggedObject $object
     */
    public function __construct(AbstractTaggedObject $object)
    {
        $this->object = $object;
    }

    /**
     * @param $content string
     *
     * @return CertStatus
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        $object = ASN1Object::fromFile($content);

        if (!$object instanceof AbstractTaggedObject)
            throw new FormatException('CertStatus must be type of AbstractTaggedObject');

        $map = (new Mapper())->map($object, Maps\CertStatus::MAP);

        if ($map === null) {
            throw new FormatException('CertStatus invalid format');
        }

        return new self($object);
    }

    /**
     * @return bool
     */
    public function isGood()
    {
        return $this->object->getIdentifier()->getTagNumber() == 0;
    }

    /**
     * @return bool
     */
    public function isRevoked()
    {
        return $this->object->getIdentifier()->getTagNumber() == 1;
    }

    /**
     * @return bool
     */
    public function isUnknown()
    {
        return $this->object->getIdentifier()->getTagNumber() == 2;
    }
}
