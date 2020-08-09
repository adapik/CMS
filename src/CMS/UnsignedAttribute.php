<?php

namespace Adapik\CMS;

use Exception;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

class UnsignedAttribute extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @return string
     */
    final public static function getOid()
    {
        return static::$oid;
    }

    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\UnsignedAttribute::class, Sequence::class));
    }

    /**
     * @return ObjectIdentifier|ASN1ObjectInterface
     * @throws ParserException
     */
    public function getIdentifier()
    {
        $binary = $this->object->getChildren()[0]->getBinary();

        return ObjectIdentifier::fromBinary($binary);
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Set
     * @throws Exception
     */
    public function getValue()
    {
        return $this->object->findChildrenByType(Set::class)[0];
    }
}
