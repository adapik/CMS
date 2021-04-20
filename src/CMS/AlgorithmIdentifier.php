<?php
/**
 * AlgorithmIdentifier
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\Universal\Sequence;

/**
 * Class AlgorithmIdentifier
 *
 * @see     Maps\AlgorithmIdentifier
 * @package Adapik\CMS
 */
class AlgorithmIdentifier extends CMSBase
{
    /** @var Sequence $object */
    protected $object;

    /**
     * @param string $content
     * @return AlgorithmIdentifier
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\AlgorithmIdentifier::class, Sequence::class));
    }

    /**
     * @return string HASH Algorithm OID representation
     */
    public function getAlgorithmOid(): string
    {
        return $this->object->getChildren()[0]->__toString();
    }
}
