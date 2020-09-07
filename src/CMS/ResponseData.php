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
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class ResponseData
 *
 * @see     Maps\ResponseData
 * @package Adapik\CMS
 */
class ResponseData extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return ResponseData
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\ResponseData::class, Sequence::class));
    }

    /**
     * @return Extension[]
     * @throws Exception
     */
    public function getExtensions()
    {
        $extensions = [];
        $taggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        if (count($taggedObjects) == 2) {
            foreach ($taggedObjects[1]->getChildren()[0]->getChildren() as $child) {
                $extensions[] = new Extension($child);
            }
        }

        return $extensions;
    }

    /**
	 * @return ASN1ObjectInterface|GeneralizedTime
     * @throws Exception
     */
    public function getProducedAt()
    {
        /** @var GeneralizedTime $producedAt */
		$binary = $this->object->findChildrenByType(GeneralizedTime::class)[0]->getBinary();

		return GeneralizedTime::fromBinary($binary);
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Sequence|OctetString
     * @throws Exception
     */
    public function getResponderID()
    {
        /** @var ExplicitlyTaggedObject $responderID */
        $responderID = $this->object->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return $responderID->getChildren()[0];
    }

    /**
     * @return SingleResponse[]
     * @throws Exception
     */
    public function getResponses()
    {
        $responses = $this->object->findChildrenByType(Sequence::class)[0];

        $singleResponses = [];

        /** @var Sequence $response */
        foreach ($responses->getChildren() as $response) {
            $singleResponses[] = new SingleResponse($response);
        }

        return $singleResponses;
    }
}
