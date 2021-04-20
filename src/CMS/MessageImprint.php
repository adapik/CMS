<?php
/**
 * MessageImprint
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class MessageImprint
 *
 * @see     Maps\MessageImprint
 * @package Adapik\CMS
 */
class MessageImprint extends CMSBase
{
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\MessageImprint::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getHashAlgorithm(): AlgorithmIdentifier
    {
        $digestAlgorithm = $this->object->getChildren()[0];

        return new AlgorithmIdentifier($digestAlgorithm);
    }

    /**
     * @return OctetString
     */
    public function getHashedMessage(): OctetString
    {
        return OctetString::createFromString($this->object->getChildren()[1]->getBinaryContent());
    }
}
