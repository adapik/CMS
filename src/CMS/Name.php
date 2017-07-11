<?php

namespace Adapik\CMS;

use FG\ASN1\Universal\Sequence;

/**
 * RDN
 */
class Name
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * Name constructor.
     *
     * @param Sequence $sequence
     */
    public function __construct(Sequence $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = [];

        foreach ($this->sequence->getChildren() as $set) {
            $sequence = $set->getChildren()[0];
            $oid      = (string) $sequence->getChildren()[0];
            $value    = (string) $sequence->getChildren()[1];
            $string[] = $oid.': '.$value;
        }

        return implode('; ', $string);
    }
}
