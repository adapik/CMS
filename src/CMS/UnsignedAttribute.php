<?php

namespace Adapik\CMS;

use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

class UnsignedAttribute extends CMSBase
{
    /**
     * @return string
     */
    final public static function getOid(): string
    {
        return static::$oid;
    }

    public static function createFromContent(string $content): CMSInterface
    {
        throw new Exception("Unsigned attribute can't be created such way");
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
     * @return Set|ASN1ObjectInterface
     * @throws Exception
     */
    public function getValue(): Set
    {
        return $this->object->findChildrenByType(Set::class)[0]->detach();
    }
}
