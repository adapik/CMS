<?php

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\Universal\Sequence;

/**
 * Class Name
 * @see Maps\Name
 * @package Adapik\CMS
 */
class Name extends CMSBase
{
    /**
     * @param string $content
     * @return Name
     * @throws Exception\FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\Name::class, Sequence::class));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = [];

        foreach ($this->object->getChildren() as $set) {
            $sequence = $set->getChildren()[0];
            $oid = (string)$sequence->getChildren()[0];
            $value = (string)$sequence->getChildren()[1];
            $string[] = $oid . ': ' . $value;
        }

        return implode('; ', $string);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->object->getChildren() as $set) {
            $sequence = $set->getChildren()[0];
            $oid = (string)$sequence->getChildren()[0];
            $value = (string)$sequence->getChildren()[1];
            $array[$oid] = $value;
        }

        return $array;
    }
}
