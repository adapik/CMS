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
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1\AbstractTaggedObject;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Sequence;

/**
 * Class SingleResponse
 *
 * @see     Maps\SingleResponse
 * @package Adapik\CMS
 */
class SingleResponse extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return SingleResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\SingleResponse::class, Sequence::class));
    }

    /**
     * @return CertID
     * @throws Exception
     */
    public function getCertID(): CertID
    {
        $certID = $this->object->findChildrenByType(Sequence::class)[0];

        return new CertID($certID);
    }

    /**
     * @return CertStatus
     * @throws Exception
     */
    public function getCertStatus(): CertStatus
    {
        $certStatus = $this->object->findChildrenByType(AbstractTaggedObject::class)[0];

        return new CertStatus($certStatus);
    }

    /**
     * @return GeneralizedTime
     * @throws Exception
     */
    public function getThisUpdate(): GeneralizedTime
    {
        /** @var GeneralizedTime $GeneralizedTime */
        $GeneralizedTime = $this->object->findChildrenByType(GeneralizedTime::class)[0]->detach();

        return $GeneralizedTime;
    }

    /**
     * @return ExplicitlyTaggedObject|ASN1ObjectInterface|null
     * @throws Exception
     */
    public function getNextUpdate(): ?ExplicitlyTaggedObject
    {
        $return = null;
        /** @var ExplicitlyTaggedObject[] $taggedObjects */
        $taggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($taggedObjects as $taggedObject) {
            if ($taggedObject->getIdentifier()->getTagNumber() == 0) {
                $return = $taggedObject->detach();
                break;
            }
        }
        return $return;
    }

    /**
     * @return ExplicitlyTaggedObject|ASN1ObjectInterface|null
     * @throws Exception
     */
    public function getSingleExtensions(): ?ExplicitlyTaggedObject
    {
        $return = null;
        /** @var ExplicitlyTaggedObject[] $taggedObjects */
        $taggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($taggedObjects as $taggedObject) {
            if ($taggedObject->getIdentifier()->getTagNumber() == 1) {
                $return = $taggedObject->detach();
            }
        }

        return $return;
    }
}
