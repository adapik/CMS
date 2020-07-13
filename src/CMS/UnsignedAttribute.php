<?php

namespace Adapik\CMS;

use Exception;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

abstract class UnsignedAttribute extends CMSBase
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

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Set
     * @throws Exception
     */
    public function getAttributeValue()
    {
        return $this->object->findChildrenByType(Set::class)[0];
    }
}
