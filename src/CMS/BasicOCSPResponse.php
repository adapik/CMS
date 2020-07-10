<?php
/**
 * BasicOCSPResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class BasicOCSPResponse
 *
 * @see     Maps\BasicOCSPResponse
 * @package Adapik\CMS
 */
class BasicOCSPResponse
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * BasicOCSPResponse constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param OctetString $content
     * @return BasicOCSPResponse
     * @throws FormatException
     */
    public static function createFromOctetString(OctetString $content)
    {
        return self::createFromContent(base64_encode($content->getBinaryContent()));
    }

    /**
     * @param $content string
     *
     * @return BasicOCSPResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence)
            throw new FormatException('BasicOCSPResponse must be type of Sequence');

        $map = (new Mapper())->map($sequence, Maps\BasicOCSPResponse::MAP);

        if ($map === null) {
            throw new FormatException('BasicOCSPResponse invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return Sequence
     */
    public function getSignatureAlgorithm()
    {
        return $this->sequence->getChildren()[1];
    }

    /**
     * @return Certificate[]
     * @throws Exception
     */
    public function getCerts()
    {
        /** @var ExplicitlyTaggedObject[] $certs */
        $certs = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);
        if (count($certs)) {
            $certificates = [];
            /** @var Sequence $child */
            foreach ($certs[0]->getChildren() as $child) {
                $certificates[] = Certificate::createFromContent($child->getBinaryContent());
            }

            return $certificates;
        }

        return [];
    }

    /**
     * @return ResponseData
     * @throws FormatException
     */
    public function getTbsResponseData()
    {
        $tbsResponseData = $this->sequence->getChildren()[0];

        return ResponseData::createFromContent($tbsResponseData->getBinary());
    }

    /**
     * @return BitString
     */
    public function getSignature()
    {
        return $this->sequence->getChildren()[2];
    }

    /**
     * @return string
     */
    public function getBinary() {
        return $this->sequence->getBinary();
    }
}