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
use FG\ASN1\Mapper\Mapper;
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
class TSTInfo
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * TSTInfo constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content
     *
     * @return TSTInfo
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('TSTInfo must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\TSTInfo::MAP);

        if ($map === null) {
            throw new FormatException('TSTInfo invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return ObjectIdentifier
     * @throws Exception
     */
    public function getPolicy()
    {
        return $this->sequence->findChildrenByType(ObjectIdentifier::class)[0];
    }

    /**
     * @return Sequence
     * @throws Exception
     */
    public function getMessageImprint()
    {
        return $this->sequence->findChildrenByType(Sequence::class)[0];
    }

    /**
     * @return Integer
     * @throws Exception
     */
    public function getSerialNumber()
    {
        return $this->sequence->findChildrenByType(Integer::class)[1];
    }

    /**
     * @return GeneralizedTime
     * @throws Exception
     */
    public function getGenTime()
    {
        return $this->sequence->findChildrenByType(GeneralizedTime::class)[0];
    }

    /**
     * @return Sequence|null
     * @throws Exception
     */
    public function getAccuracy()
    {
        /** @var Sequence[] $sequences */
        $sequences = $this->sequence->findChildrenByType(Sequence::class);
        if (count($sequences) > 1) {
            return $sequences[1];
        }

        return null;
    }

    /**
     * @return Boolean|null
     * @throws Exception
     */
    public function getOrdering()
    {
        /** @var Boolean[] $booleans */
        $booleans = $this->sequence->findChildrenByType(Boolean::class);
        if (count($booleans)) {
            return $booleans[0];
        }

        return null;
    }

    public function getNonce()
    {
        $integers = $this->sequence->findChildrenByType(Integer::class);
        if (count($integers) == 3) {
            return $integers[2];
        }
        return null;
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getTsa()
    {
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 0) {
                return $explicit;
            }
        }
        return null;
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 1) {
                return $explicit;
            }
        }
        return null;
    }
}
