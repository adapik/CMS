<?php
/**
 * TimeStampResponse
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
 * Class TimeStampResponse
 *
 * @see     Maps\TimeStampResponse
 * @package Adapik\CMS
 */
class TimeStampResponse
{
    const CONTENT_TYPE = 'application/timestamp-reply';

    /**
     * @var Sequence
     */
    protected $sequence;

    /**
     * TimeStampRequest constructor.
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
     * @return TimeStampResponse
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('TimeStampResponse must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\TimeStampResponse::MAP);

        if ($map === null) {
            throw new FormatException('TimeStampResponse invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return PKIStatusInfo
     */
    public function getStatusInfo()
    {
        return new PKIStatusInfo($this->sequence->getChildren()[0]);
    }

    /**
     * @return SignedData|null
     */
    public function getTimeStampToken()
    {
        $children = $this->sequence->getChildren();

        if (count($children) == 2) {
            return new SignedData($children[1]);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->sequence->getBinary();
    }
}
