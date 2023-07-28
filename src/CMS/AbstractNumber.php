<?php

declare(strict_types=1);

namespace Adapik\CMS;


abstract class AbstractNumber
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function toBinary(): string
    {
        return $this->value;
    }

    public function toInt(): string
    {
        return gmp_strval(gmp_init($this->toHex(), 16));
    }

    public function toHex(): string
    {
        return bin2hex($this->value);
    }

    public function __toString(): string
    {
        return $this->toInt();
    }
}
