<?php
/**
 * Signature
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

/**
 * Class Signature
 *
 * @see     Maps\Signature
 * @package Adapik\CMS
 */
class Signature extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return Signature
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\Signature::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getSignatureAlgorithm()
    {
        $signatureAlgorithm = $this->object->getChildren()[0];

        return new AlgorithmIdentifier($signatureAlgorithm);
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return BitString
     */
    public function getSignature()
    {
        return $this->object->getChildren()[1];
    }

    /**
     * FIXME: shouldn't be created from content
     * @return Certificate[]
     * @throws FormatException
     */
    public function getCerts()
    {
        $certificates = [];

        if (count($this->object->getChildren()) == 3) {
            /** @var ExplicitlyTaggedObject $certs */
            $certs = $this->object->getChildren()[2];

            foreach ($certs->getChildren() as $cert) {
                $certificates[] = Certificate::createFromContent($cert->getBinaryContent());
            }
        }
        return $certificates;
    }
}
