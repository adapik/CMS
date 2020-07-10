<?php
/**
 * OCSPResponse
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
 * Class OCSPResponse
 *
 * @see     Maps\OCSPResponse
 * @package Adapik\CMS
 */
class OCSPResponse
{
    const CONTENT_TYPE = 'application/ocsp-response';
    const OID_OCSP_BASIC = "1.3.6.1.5.5.7.48.1.1";
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * OCSPResponse constructor.
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
     * @return OCSPResponse
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('OCSPResponse must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\OCSPResponse::MAP);

        if ($map === null) {
            throw new FormatException('OCSPResponse invalid format');
        }

        return new self($sequence);
    }


    /**
     * @return ResponseBytes|null
     * @throws FormatException
     */
    public function getResponseBytes()
    {
        $children = $this->sequence->getChildren();

        if (count($children) == 2) {
            return ResponseBytes::createFromContent($children[1]->getBinaryContent());
        }

        return null;
    }

    /**
     * @return OCSPResponseStatus
     * @throws FormatException
     */
    public function getResponseStatus()
    {
        $enum = $this->sequence->getChildren()[0];

        return OCSPResponseStatus::createFromContent($enum->getBinary());
    }

    /**
     * @return BasicOCSPResponse
     * @throws FormatException
     */
    public function getBasicOCSPResponse()
    {
        return BasicOCSPResponse::createFromOctetString($this->getResponseBytes()->getResponse());
    }
}
