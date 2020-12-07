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
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

/**
 * Class CertificateList
 *
 * @see     Maps\CertificateList
 * @package Adapik\CMS
 */
class CertificateList extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return CertificateList
     * @throws FormatException
     */
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\CertificateList::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getSignatureAlgorithm(): AlgorithmIdentifier
    {
        return new AlgorithmIdentifier($this->object->getChildren()[1]);
    }

    /**
     * @return ASN1ObjectInterface|BitString
     * @throws ParserException
     */
    public function getSignature(): BitString
    {
        $binary = $this->object->findChildrenByType(BitString::class)[0]->getBinary();

        return BitString::fromBinary($binary);
    }

    /**
     * @return TBSCertList
     */
    public function getTBSCertList(): TBSCertList
    {
        return new TBSCertList($this->object->getChildren()[0]);
    }
}
