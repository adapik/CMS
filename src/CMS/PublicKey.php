<?php
/**
 * PublicKey
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

/**
 * Class PublicKey
 *
 * @see     Maps\PublicKey
 * @package Adapik\CMS
 */
class PublicKey extends PEMBase
{
    const PEM_HEADER = "BEGIN PUBLIC KEY";
    const PEM_FOOTER = "END PUBLIC KEY";

    /**
     * @param string $content
     *
     * @return PublicKey
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\PublicKey::class, Sequence::class));
    }

    /**
     * @return BitString|ASN1ObjectInterface
     */
    public function getKey(): BitString
    {
        /** @var BitString $bitString */
        $bitString = $this->object->getChildren()[1];

        return $bitString->detach();
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getKeyAlgorithm(): AlgorithmIdentifier
    {
        return new AlgorithmIdentifier($this->object->getChildren()[0]);
    }
}
