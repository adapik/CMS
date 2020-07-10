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
class Signature
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * Signature constructor.
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
     * @return Signature
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('Signature must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\Signature::MAP);

        if ($map === null) {
            throw new FormatException('Signature invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return AlgorithmIdentifier
     * @throws FormatException
     */
    public function getSignatureAlgorithm()
    {
        $signatureAlgorithm = $this->sequence->getChildren()[0];

        return AlgorithmIdentifier::createFromContent($signatureAlgorithm->getBinary());
    }

    /**
     * @return BitString
     */
    public function getSignature()
    {
        return $this->sequence->getChildren()[1];
    }

    /**
     * @return Certificate[]
     * @throws FormatException
     */
    public function getCerts()
    {
        $certificates = [];

        if (count($this->sequence->getChildren()) == 3) {
            /** @var ExplicitlyTaggedObject $certs */
            $certs = $this->sequence->getChildren()[2];

            foreach ($certs->getChildren() as $cert) {
                $certificates[] = Certificate::createFromContent($cert->getBinaryContent());
            }
        }
        return $certificates;
    }
}
