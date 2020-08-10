<?php
/**
 * Extension
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class Extension
 *
 * @see     Maps\Extension
 * @package Adapik\CMS
 */
class Extension extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return Extension
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\Extension::class, Sequence::class));
    }

    /**
     * @return ASN1ObjectInterface
     * @throws ParserException
     */
    public function getExtensionId()
    {
        $binary = $this->object->getChildren()[0]->getBinary();

        return ObjectIdentifier::fromBinary($binary);
    }

    /**
     * @return Boolean|null
     * @throws ParserException
     */
    public function isCritical()
    {
        $booleans = $this->object->findChildrenByType(Boolean::class);
        if (count($booleans) > 0) {
            $binary = $booleans[0]->getBinary();
            return Boolean::fromBinary($binary);
        }

        return null;
    }

    /**
     * @return OctetString|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getExtensionValue()
    {
        $octets = $this->object->findChildrenByType(OctetString::class);
        $binary = $octets[0]->getBinary();
        return OctetString::fromBinary($binary);
    }
}
