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
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class BasicOCSPResponse
 *
 * @see     Maps\BasicOCSPResponse
 * @package Adapik\CMS
 */
class BasicOCSPResponse extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

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
     * @param string $content
     * @return BasicOCSPResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\BasicOCSPResponse::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getSignatureAlgorithm()
    {
        return new AlgorithmIdentifier($this->object->getChildren()[1]);
    }

    /**
     * @return Certificate[]
     * @throws Exception
     */
    public function getCerts()
    {
        /** @var ExplicitlyTaggedObject[] $certs */
        $certs = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        if (count($certs)) {
            $certificates = [];
            /** @var Sequence $child */
            foreach ($certs[0]->getChildren() as $child) {
                $certificates[] = new Certificate($child);
            }

            return $certificates;
        }

        return [];
    }

    /**
     * @return ResponseData
     */
    public function getTbsResponseData()
    {
        $tbsResponseData = $this->object->getChildren()[0];

        return new ResponseData($tbsResponseData);
    }

    /**
     * @return BitString
     * @throws ParserException
     */
    public function getSignature()
    {
        $binary = $this->object->getChildren()[2]->getBinary();
        return BitString::fromBinary($binary);
    }
}
