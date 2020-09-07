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
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class CertID
 *
 * @see     Maps\CertID
 * @package Adapik\CMS
 */
class CertID extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return CertID
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\CertID::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getHashAlgorithm()
    {
        return new AlgorithmIdentifier($this->object->getChildren()[0]);
    }

    /**
     * @return OctetString|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getIssuerKeyHash()
    {
        $binary = $this->object->getChildren()[2]->getBinary();

        return OctetString::fromBinary($binary);
    }

    /**
     * @return OctetString|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getIssuerNameHash()
    {
        $binary = $this->object->getChildren()[1]->getBinary();

        return OctetString::fromBinary($binary);
    }

    /**
     * @return Integer|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getSerialNumber()
    {
        $binary = $this->object->getChildren()[3]->getBinary();

        return Integer::fromBinary($binary);
    }
}
