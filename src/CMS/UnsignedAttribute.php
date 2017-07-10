<?php

namespace Adapik\CMS;

abstract class UnsignedAttribute
{
    final public static function getOid()
    {
        return static::$oid;
    }
}
