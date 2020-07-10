<?php
/**
 * CertID
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
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class CertID
 *
 * @see     Maps\CertID
 * @package Adapik\CMS
 */
class CertID
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * CertID constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content string
     *
     * @return CertID
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence)
            throw new FormatException('CertID must be type of Sequence');

        $map = (new Mapper())->map($sequence, Maps\CertID::MAP);

        if ($map === null) {
            throw new FormatException('CertID invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return Sequence
     */
    public function getHashAlgorithm()
    {
        return $this->sequence->getChildren()[0];
    }

    /**
     * @return OctetString
     */
    public function getIssuerKeyHash()
    {
        return $this->sequence->getChildren()[2];
    }

    /**
     * @return OctetString
     */
    public function getIssuerNameHash()
    {
        return $this->sequence->getChildren()[1];
    }

    /**
     * @return Integer
     */
    public function getSerialNumber()
    {
        return $this->sequence->getChildren()[3];
    }
}