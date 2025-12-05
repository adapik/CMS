<?php
/**
 * PrivateKey
 *
 * @author    Alexander Danilov <adapik@yandex.ru>
 * @copyright 2025 Alexander Danilov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use Adapik\CMS\Interfaces\PEMConvertable;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class PrivateKey
 *
 * @see     Maps\PrivateKeyInfo
 * @package Adapik\CMS
 */
class PrivateKey extends CMSBase implements PEMConvertable
{
    const PEM_HEADER = "BEGIN PRIVATE KEY";
    const PEM_FOOTER = "END PRIVATE KEY";

    /**
     * @param string $content
     *
     * @return PrivateKey
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\PrivateKeyInfo::class, Sequence::class));
    }

    /**
     * @return OctetString|ASN1ObjectInterface
     */
    public function getKey(): OctetString
    {
        /** @var OctetString $octetString */
        $octetString = $this->object->getChildren()[2];

        return $octetString->detach();
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getKeyAlgorithm(): AlgorithmIdentifier
    {
        return new AlgorithmIdentifier($this->object->getChildren()[1]);
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        /** @var \FG\ASN1\Universal\Integer $version */
        $version = $this->object->getChildren()[0];

        return (int)$version->__toString();
    }

    /**
     * @return string
     */
    public function getPEMHeader(): string
    {
        return self::PEM_HEADER;
    }

    /**
     * @return string
     */
    public function getPEMFooter(): string
    {
        return self::PEM_FOOTER;
    }
}
