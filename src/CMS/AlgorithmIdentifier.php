<?php
/**
 * AlgorithmIdentifier
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
 * Class AlgorithmIdentifier
 *
 * @see     Maps\AlgorithmIdentifier
 * @package Adapik\CMS
 */
class AlgorithmIdentifier
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * AlgorithmIdentifier constructor.
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
     * @return AlgorithmIdentifier
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('AlgorithmIdentifier must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\AlgorithmIdentifier::MAP);

        if ($map === null) {
            throw new FormatException('AlgorithmIdentifier invalid format');
        }

        return new self($sequence);
    }
}