<?php
/**
 * EncapsulatedContentInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1Object;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class EncapsulatedContentInfo.
 * @see     Maps\EncapsulatedContentInfo
 * @package Adapik\CMS
 */
class EncapsulatedContentInfo
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * EncapsulatedContentInfo constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content
     * @return EncapsulatedContentInfo
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('EncapsulatedContentInfo must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\EncapsulatedContentInfo::MAP);

        if ($map === null) {
            throw new FormatException('EncapsulatedContentInfo invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return OctetString[]
     * @throws ParserException
     */
    public function getEContent()
    {
        $eContentSet = $this->sequence->getChildren()[1];

        $OctetStrings = [];
        foreach ($eContentSet->getChildren() As $octetString) {
            $binary = $octetString->getBinary();
            $OctetStrings[] = OctetString::fromBinary($binary);
        }

        return $OctetStrings;
    }
}
