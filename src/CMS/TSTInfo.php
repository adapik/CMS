<?php
/**
 * TSTInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

/**
 * Class TSTInfo
 *
 * @see     Maps\TSTInfo
 * @package Adapik\CMS
 */
class TSTInfo extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return TSTInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TSTInfo::class, Sequence::class));
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ObjectIdentifier
     * @throws Exception
     */
    public function getPolicy()
    {
        return $this->object->findChildrenByType(ObjectIdentifier::class)[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Sequence
     * @throws Exception
     */
    public function getMessageImprint()
    {
        return $this->object->findChildrenByType(Sequence::class)[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Integer
     * @throws Exception
     */
    public function getSerialNumber()
    {
        return $this->object->findChildrenByType(Integer::class)[1];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return GeneralizedTime
     * @throws Exception
     */
    public function getGenTime()
    {
        return $this->object->findChildrenByType(GeneralizedTime::class)[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Sequence|null
     * @throws Exception
     */
    public function getAccuracy()
    {
        /** @var Sequence[] $sequences */
        $sequences = $this->object->findChildrenByType(Sequence::class);
        if (count($sequences) > 1) {
            return $sequences[1];
        }

        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Boolean|null
     * @throws Exception
     */
    public function getOrdering()
    {
        /** @var Boolean[] $booleans */
        $booleans = $this->object->findChildrenByType(Boolean::class);
        if (count($booleans)) {
            return $booleans[0];
        }

        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ASN1Object|null
     * @throws Exception
     */
    public function getNonce()
    {
        $integers = $this->object->findChildrenByType(Integer::class);
        if (count($integers) == 3) {
            return $integers[2];
        }
        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getTsa()
    {
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 0) {
                return $explicit;
            }
        }
        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 1) {
                return $explicit;
            }
        }
        return null;
    }
}
