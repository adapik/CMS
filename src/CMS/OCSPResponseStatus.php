<?php
/**
 * OCSPResponseStatus
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
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\Sequence;

/**
 * Class OCSPResponseStatus
 *
 * @see     Maps\OCSPResponseStatus
 * @package Adapik\CMS
 */
class OCSPResponseStatus
{
    /**
     * @var Sequence
     */
    private $enum;

    /**
     * OCSPResponseStatus constructor.
     *
     * @param Enumerated $object
     */
    public function __construct(Enumerated $object)
    {
        $this->enum = $object;
    }

    /**
     * @param $content
     *
     * @return OCSPResponseStatus
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $enum = ASN1Object::fromFile($content);

        if (!$enum instanceof Enumerated) {
            throw new FormatException('OCSPResponseStatus must be type of Enumerated');
        }

        $map = (new Mapper())->map($enum, Maps\OCSPResponseStatus::MAP);

        if ($map === null) {
            throw new FormatException('OCSPResponseStatus invalid format');
        }

        return new self($enum);
    }

    public function getMapping() {
        return Maps\OCSPResponseStatus::MAP['mapping'];
    }

    /**
     * Returns status of request. 0 = is OK, other - NOT
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return intval($this->enum->value) === 0;
    }
}
