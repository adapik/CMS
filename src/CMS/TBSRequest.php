<?php
/**
 * TBSRequest
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
use FG\ASN1\Universal\Sequence;

/**
 * Class TBSRequest
 *
 * @see     Maps\TBSRequest
 * @package Adapik\CMS
 */
class TBSRequest
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * TBSRequest constructor.
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
     * @return TBSRequest
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('TBSRequest must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\TBSRequest::MAP);

        if ($map === null) {
            throw new FormatException('TBSRequest invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getRequestorName()
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 1) {
                return $tag;
            }
        }
        return null;
    }

    /**
     * @return Request[]
     * @throws FormatException
     */
    public function getRequestList()
    {
        $requests = [];
        /** @var Sequence[] $requestList */
        $requestList = $this->sequence->findChildrenByType(Sequence::class);
        foreach ($requestList as $sequence) {
            $requests[] = Request::createFromContent($sequence->getBinaryContent());
        }

        return $requests;
    }

    /**
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getRequestExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);

        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 2) {
                return $tag;
            }
        }

        return null;
    }
}