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
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\ImplicitlyTaggedObject;

/**
 * Class CertStatus
 * In case of successful check it is ExplicitlyTaggedObject, otherwise ImplicitlyTaggedObject
 *
 * @see     Maps\CertStatus
 * @package Adapik\CMS
 */
class CertStatus extends CMSBase
{
    /**
     * @var ImplicitlyTaggedObject|ExplicitlyTaggedObject
     */
    protected $object;

    /**
     * @param string $content
     * @return CertStatus
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, Maps\CertStatus::class, AbstractTaggedObject::class));
    }

    /**
     * @return bool
     */
    public function isGood(): bool
    {
        return $this->object->getIdentifier()->getTagNumber() == 0;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->object->getIdentifier()->getTagNumber() == 1;
    }

    /**
     * @return bool
     */
    public function isUnknown(): bool
    {
        return $this->object->getIdentifier()->getTagNumber() == 2;
    }
}
