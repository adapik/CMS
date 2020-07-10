<?php

namespace Adapik\CMS;

use Exception;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

abstract class UnsignedAttribute
{
    /**
     * @var Sequence
     */
    protected $sequence;

    final public static function getOid()
    {
        return static::$oid;
    }

    /**
     * @return Set
     * @throws Exception
     */
    public function getAttributeValue()
    {
        return $this->sequence->findChildrenByType(Set::class)[0];
    }
}
