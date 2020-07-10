<?php
/**
 * TimeStampToken
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1Object;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Sequence;

/**
 * Class TimeStampToken
 *
 * @see     Maps\TimeStampToken
 * @package Adapik\CMS
 */
class TimeStampToken extends UnsignedAttribute
{
    protected static $oid = '1.2.840.113549.1.9.16.2.14';

    /**
     * TimeStampToken constructor.
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
     * @return TimeStampToken
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('TimeStampToken must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\TimeStampToken::MAP);

        if ($map === null) {
            throw new FormatException('TimeStampToken invalid format');
        }

        return new self($sequence);
    }

    public function getTSTInfo()
    {
        /** @var EncapsulatedContentInfo $SignedDataContent */
        $EContent = $this->getSignedData()->getSignedDataContent()->getEncapsulatedContentInfo()->getEContent();

        return TSTInfo::createFromContent($EContent[0]->getBinaryContent());
    }

    /**
     * @return SignedData
     * @throws FormatException
     */
    public function getSignedData()
    {
        $SignedData = $this->sequence->getChildren()[1]->getChildren()[0];

        return SignedData::createFromContent($SignedData->getBinary());
    }
}
