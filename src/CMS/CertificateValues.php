<?php

namespace Adapik\CMS;

class CertificateValues extends UnsignedAttribute
{
    protected static $oid = '1.2.840.113549.1.9.16.2.23';

    public static function createFromContent(string $content)
    {
        // TODO: Implement createFromContent() method.
    }
}
