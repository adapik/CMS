<?php
/**
 * CertificateList
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
 * Class CertificateList
 *
 * @see     Maps\CertificateList
 * @package Adapik\CMS
 */
class CertificateList
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * CertificateList constructor.
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
     * @return CertificateList
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('CertificateList must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\CertificateList::MAP);

        if ($map === null) {
            throw new FormatException('CertificateList invalid format');
        }

        return new self($sequence);
    }

}