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
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
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
     * @param string $content
     *
     * @return TSTInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\TSTInfo::class, Sequence::class));
    }

    /**
     * @return Accuracy|null
     * @throws Exception
     */
    public function getAccuracy(): ?Accuracy
    {
        $return = null;
        /** @var Sequence[] $sequences */
        $sequences = $this->object->findChildrenByType(Sequence::class);
        if (count($sequences) > 1) {
            $return = new Accuracy($sequences[1]);
        }

        return $return;
    }

    /**
     * @return GeneralizedTime|ASN1ObjectInterface
     * @throws Exception
     */
    public function getGenTime(): GeneralizedTime
    {
        $binary = $this->object->findChildrenByType(GeneralizedTime::class)[0]->getBinary();

        return GeneralizedTime::fromBinary($binary);
    }

    /**
     * @return MessageImprint
     * @throws Exception
     */
    public function getMessageImprint(): MessageImprint
    {
        return new MessageImprint($this->object->findChildrenByType(Sequence::class)[0]);
    }

    /**
     * @return Integer|ASN1ObjectInterface
     * @throws Exception
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getNonce(): ?\FG\ASN1\Universal\Integer
    {
        $return = null;
        $integers = $this->object->findChildrenByType(Integer::class);
        if (count($integers) == 3) {
            $binary = $integers[2]->getBinary();

            $return = Integer::fromBinary($binary);
        }

        return $return;
    }

    /**
     * @return Boolean|null
     * @throws Exception
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getOrdering(): ?\FG\ASN1\Universal\Boolean
    {
        $return = null;
        /** @var Boolean[] $booleans */
        $booleans = $this->object->findChildrenByType(Boolean::class);
        if (count($booleans)) {
            $binary = $booleans[0]->getBinary();

            $return = Boolean::fromBinary($binary);
        }

        return $return;
    }

    /**
     * @return ObjectIdentifier|ASN1ObjectInterface
     * @throws Exception
     */
    public function getPolicy(): ObjectIdentifier
    {
        $binary = $this->object->findChildrenByType(ObjectIdentifier::class)[0]->getBinary();

        return ObjectIdentifier::fromBinary($binary);
    }

    /**
     * @return Integer|ASN1ObjectInterface
     * @throws Exception
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getSerialNumber(): ?\FG\ASN1\Universal\Integer
    {
        $binary = $this->object->findChildrenByType(Integer::class)[1]->getBinary();

        return Integer::fromBinary($binary);
    }

    /**
     * TODO: create CMS and check correctness
     *
     * @return GeneralName|null
     * @throws Exception
     */
    public function getTsa(): ?GeneralName
    {
        $return = null;
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 0) {
                $return = new GeneralName($explicit);
            }
        }

        return $return;
    }
}
