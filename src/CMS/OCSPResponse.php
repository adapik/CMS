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
class OCSPResponse extends CMSBase
{
    const CONTENT_TYPE = 'application/ocsp-response';
    const OID_OCSP_BASIC = "1.3.6.1.5.5.7.48.1.1";
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return OCSPResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\OCSPResponse::class, Sequence::class));
    }

    /**
     * @return ResponseBytes|null
     * @throws FormatException
     */
    public function getResponseBytes()
    {
        $children = $this->object->getChildren();

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
        $enum = $this->object->getChildren()[0];

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
