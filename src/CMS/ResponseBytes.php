<?php
/**
 * ResponseBytes
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
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class ResponseBytes
 *
 * @see     Maps\ResponseBytes
 * @package Adapik\CMS
 */
class ResponseBytes
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * ResponseBytes constructor.
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
     * @return ResponseBytes
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('ResponseBytes must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\ResponseBytes::MAP);

        if ($map === null) {
            throw new FormatException('ResponseBytes invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return ObjectIdentifier
     */
    public function getResponseType()
    {
        return $this->sequence->getChildren()[0];
    }

    /**
     * @return OctetString
     */
    public function getResponse()
    {
        return $this->sequence->getChildren()[1];
    }
}
