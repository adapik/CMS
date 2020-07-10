<?php
/**
 * ResponseData
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
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class ResponseData
 *
 * @see     Maps\ResponseData
 * @package Adapik\CMS
 */
class ResponseData
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * ResponseData constructor.
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
     * @return ResponseData
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence)
            throw new FormatException('ResponseData must be type of Sequence');

        $map = (new Mapper())->map($sequence, Maps\ResponseData::MAP);

        if ($map === null) {
            throw new FormatException('ResponseData invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return ExplicitlyTaggedObject
     * @throws Exception
     */
    public function getExtensions()
    {
        $taggedObjects = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        if (count($taggedObjects) == 2) {
            return $taggedObjects[1];
        }

        return null;
    }

    /**
     * @return GeneralizedTime
     * @throws Exception
     */
    public function getProducedAt()
    {
        /** @var GeneralizedTime $producedAt */
        $producedAt = $this->sequence->findChildrenByType(GeneralizedTime::class)[0];

        return $producedAt;
    }

    /**
     * @return Sequence|OctetString
     * @throws Exception
     */
    public function getResponderID()
    {
        /** @var ExplicitlyTaggedObject $responderID */
        $responderID = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return $responderID->getChildren()[0];
    }

    /**
     * @return SingleResponse[]
     * @throws Exception
     */
    public function getResponses()
    {
        $responses = $this->sequence->findChildrenByType(Sequence::class)[0];

        $singleResponses = [];

        /** @var Sequence $response */
        foreach ($responses->getChildren() as $response) {
            $singleResponses[] = new SingleResponse($response);
        }

        return $singleResponses;
    }
}
