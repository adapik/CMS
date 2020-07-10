<?php
/**
 * SingleResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\AbstractTaggedObject;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Sequence;

/**
 * Class SingleResponse
 *
 * @see     Maps\SingleResponse
 * @package Adapik\CMS
 */
class SingleResponse
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * SingleResponse constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content string
     *
     * @return SingleResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence)
            throw new FormatException('SingleResponse must be type of Sequence');

        $map = (new Mapper())->map($sequence, Maps\SingleResponse::MAP);

        if ($map === null) {
            throw new FormatException('SingleResponse invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return CertID
     * @throws Exception
     */
    public function getCertID()
    {
        $certID = $this->sequence->findChildrenByType(Sequence::class)[0];

        return CertID::createFromContent($certID->getBinary());
    }

    /**
     * @return CertStatus
     * @throws Exception
     */
    public function getCertStatus()
    {
        $certStatus = $this->sequence->findChildrenByType(AbstractTaggedObject::class)[0];

        return CertStatus::createFromContent($certStatus->getBinary());
    }

    /**
     * @return GeneralizedTime
     * @throws Exception
     */
    public function getThisUpdate()
    {
        /** @var GeneralizedTime $GeneralizedTime */
        $GeneralizedTime = $this->sequence->findChildrenByType(GeneralizedTime::class)[0];

        return $GeneralizedTime;
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getNextUpdate()
    {
        /** @var ExplicitlyTaggedObject[] $taggedObjects */
        $taggedObjects = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($taggedObjects as $taggedObject) {
            if ($taggedObject->getIdentifier()->getTagNumber() == 0) {
                return $taggedObject;
            }
        }
        return null;
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getSingleExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $taggedObjects */
        $taggedObjects = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($taggedObjects as $taggedObject) {
            if ($taggedObject->getIdentifier()->getTagNumber() == 1) {
                return $taggedObject;
            }
        }
        return null;
    }

}