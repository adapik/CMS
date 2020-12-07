<?php
/**
 * Accuracy
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;

/**
 * Class Accuracy
 *
 * @see     Maps\Accuracy
 * @package Adapik\CMS
 */
class Accuracy extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     *
     * @return Accuracy
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\Accuracy::class, Sequence::class));
    }

    /**
     * TODO: implement
     */
    //public function getMicros() {

    //}

    /**
     * TODO: implement
     */
    //public function getMillis() {

    //}

    /**
     * @return \FG\ASN1\Universal\Integer|ASN1ObjectInterface
     * @throws ParserException
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getSeconds(): \FG\ASN1\Universal\Integer
    {
        $integers = $this->object->findChildrenByType(Integer::class);

        $binary = $integers[0]->getBinary();

        return Integer::fromBinary($binary);
    }
}
