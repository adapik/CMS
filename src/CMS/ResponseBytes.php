<?php
/**
 * ResponseBytes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class ResponseBytes
 *
 * @see     Maps\ResponseBytes
 * @package Adapik\CMS
 */
class ResponseBytes extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return ResponseBytes
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\ResponseBytes::class, Sequence::class));
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ObjectIdentifier
     */
    public function getResponseType()
    {
        return $this->object->getChildren()[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return OctetString
     */
    public function getResponse()
    {
        return $this->object->getChildren()[1];
    }
}
