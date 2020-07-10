<?php
/**
 * TimeStampRequest
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
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class TimeStampRequest
 *
 * @see     Maps\TimeStampRequest
 * @package Adapik\CMS
 */
class TimeStampRequest extends RequestModel
{
    const CONTENT_TYPE = 'application/timestamp-query';

    /**
     * @var Sequence
     */
    protected $sequence;

    /**
     * TimeStampRequest constructor.
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
     * @return TimeStampRequest
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('TimeStampRequest must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\TimeStampRequest::MAP);

        if ($map === null) {
            throw new FormatException('TimeStampRequest invalid format');
        }

        return new self($sequence);
    }

    /**
     * @param OctetString $data data which should be queried with TS request
     * @param string $hashAlgorithmOID
     * @return TimeStampRequest
     * @throws Exception
     */
    public static function createSimple(OctetString $data, string $hashAlgorithmOID = Algorithm::OID_SHA256)
    {
        $tspRequest = Sequence::create([
            # version
            Integer::create(1),
            # messageImprint
            Sequence::create([
                Sequence::create([
                    ObjectIdentifier::create($hashAlgorithmOID),
                    NullObject::create(),
                ]),
                OctetString::createFromString(Algorithm::hashValue($hashAlgorithmOID, $data->getBinaryContent()))
            ]),
            # nonce
            Integer::create(rand() << 32 | rand()),
            # certReq
            Boolean::create(true),
        ]);

        return new self($tspRequest);
    }

    /**
     * @param string[] $urls
     * @return TimeStampResponse|null
     * @throws FormatException
     */
    public function processRequest(array $urls)
    {
        $this->processErrors = [];

        foreach ($urls as $url) {

            $result = $this->curlRequest($url, $this->sequence->getBinary(), self::CONTENT_TYPE, TimeStampResponse::CONTENT_TYPE);

            // Actually we need only 1 response, and if array is not set - we do not have any errors
            if (!isset($this->processErrors[$url]) && !is_null($result)) {
                return TimeStampResponse::createFromContent($result);
            }
        }

        return null;
    }


    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->sequence->getBinary();
    }

    /**
     * @return Boolean
     * @throws Exception
     */
    public function getCertReq()
    {
        return $this->sequence->findChildrenByType(Boolean::class)[0];
    }

    /**
     * @return ImplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getExtensions()
    {
        $objects = $this->sequence->findChildrenByType(ImplicitlyTaggedObject::class);

        if (count($objects)) {
            return $objects[0];
        }

        return null;
    }

    /**
     * @return Sequence
     * @throws Exception
     */
    public function getMessageImprint()
    {
        return $this->sequence->findChildrenByType(Sequence::class)[0];
    }

    /**
     * @return Integer|null
     * @throws Exception
     */
    public function getNonce()
    {
        $integers = $this->sequence->findChildrenByType(Integer::class);
        if (count($integers) == 2) {
            return $integers[1];
        }

        return null;
    }

    /**
     * @return ObjectIdentifier
     * @throws Exception
     */
    public function getReqPolicy()
    {
        $objects = $this->sequence->findChildrenByType(ObjectIdentifier::class);
        if (count($objects)) {
            return $objects[0];
        }

        return null;
    }
}