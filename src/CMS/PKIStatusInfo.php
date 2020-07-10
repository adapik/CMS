<?php
/**
 * PKIStatusInfo
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
 * Class PKIStatusInfo
 *
 * @see     Maps\PKIStatusInfo
 * @package Adapik\CMS
 */
class PKIStatusInfo
{
    /**
     * @var Sequence
     */
    protected $sequence;

    /**
     * PKIStatusInfo constructor.
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
     * @return PKIStatusInfo
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('PKIStatusInfo must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\PKIStatusInfo::MAP);

        if ($map === null) {
            throw new FormatException('PKIStatusInfo invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return bool
     */
    public function isGranted() {
        return $this->getStatus() == 0;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $integer = $this->sequence->getChildren()[0];

        return intval($integer->__toString());
    }

    /**
     * @return string
     */
    public function getFailInfo()
    {
        $children = $this->sequence->getChildren();

        if (count($children) == 2) {
            return $children[1]->__toString();
        }
        return null;
    }
}