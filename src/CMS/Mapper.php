<?php
/**
 * Created by PhpStorm.
 * User: Adapik
 * Date: 13.07.2017
 * Time: 21:51
 */

namespace Adapik\CMS;

use FG\ASN1\ASN1Object;
use FG\ASN1\Identifier;

class Mapper
{
    public static function map(ASN1Object $object, array $mapping)
    {
        if ((isset($mapping['explicit']) || isset($mapping['implicit'])) && count($object->getChildren()) > 0) {
            $object = $object->getChildren()[0];
        }

        switch (true) {
            case $mapping['type'] === Identifier::ANY:
                return $object;
                break;
            case $mapping['type'] === Identifier::CHOICE:
                foreach ($mapping['children'] as $key => $option) {
                    switch (true) {
                        case isset($option['constant']) && $option['constant'] == $object->getIdentifier()->getTagClass():
                        case !isset($option['constant']) && $option['type'] == $object->getIdentifier()->getTagNumber():
                            $value = self::map($object, $option);
                            break;
                        case !isset($option['constant']) && $option['type'] == Identifier::CHOICE:
                            $v = self::map($object, $option);
                            if (isset($v)) {
                                $value = $v;
                            }
                    }
                    if (isset($value)) {
                        return [$key => $value];
                    }
                }
                return null;
            case isset($mapping['implicit']):
            case isset($mapping['explicit']):
            case $object->getIdentifier()->getTagNumber() == $mapping['type']:
                break;
            default:
                // if $decoded['type'] and $mapping['type'] are both strings, but different types of strings,
                // let it through
                switch (true) {
                    case $object->getIdentifier()->getTagNumber() < 18: // self::TYPE_NUMERIC_STRING == 18
                    case $object->getIdentifier()->getTagNumber() > 30: // self::TYPE_BMP_STRING == 30
                    case $mapping['type'] < 18:
                    case $mapping['type'] > 30:
                        return null;
                }
        }

        switch ($object->getIdentifier()->getTagNumber()) {
            case Identifier::SEQUENCE:
                $map = [];

                // ignore the min and max
                if (isset($mapping['min']) && isset($mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($object->getChildren() as $content) {
                        if (($map[] = self::map($content, $child)) === null) {
                            return null;
                        }
                    }

                    return $map;
                }

                $n = count($object->getChildren());
                $i = 0;

                foreach ($mapping['children'] as $key => $child) {
                    $mayMatch = $i < $n; // Match only existing input.
                    if ($mayMatch) {
                        $currentChild = $object->getChildren()[$i];

                        if ($child['type'] !== Identifier::CHOICE) {
                            // Get the mapping and input class & constant.
                            $childClass = $currentChildClass = Identifier::CLASS_UNIVERSAL;
                            $constant = null;
                            $currentChildClass = $currentChild->getIdentifier()->getTagClass();
                            if (isset($child['class'])) {
                                $childClass = $child['class'];
                                $constant = $child['cast'];
                            } elseif (isset($child['constant'])) {
                                $childClass = Identifier::CLASS_CONTEXT_SPECIFIC;
                                $constant = $child['constant'];
                            }

                            if (isset($constant) && $currentChildClass) {
                                // Can only match if constants and class match.
                                $mayMatch = $constant == $currentChild['constant'] && $childClass == $currentChildClass;
                            } else {
                                // Can only match if no constant expected and type matches or is generic.
                                $mayMatch = !isset($child['constant']) && array_search($child['type'], [$currentChild['type'], self::TYPE_ANY, self::TYPE_CHOICE]) !== false;
                            }
                        }
                    }

                    if ($mayMatch) {
                        // Attempt submapping.
                        $candidate = self::asn1map($currentChild, $child, $special);
                        $mayMatch = $candidate !== null;
                    }

                    if ($mayMatch) {
                        $map[$key] = $candidate;
                        $i++;
                    } elseif (isset($child['default'])) {
                        switch ($child['type']) {
                            case ASN1::TYPE_INTEGER:
                                $map[$key] = new BigInteger($child['default']);
                                break;
                            //case ASN1::TYPE_BOOLEAN:
                            default:
                                $map[$key] = $child['type'];
                        }
                    } elseif (!isset($child['optional'])) {
                        return null; // Syntax error.
                    }
                }

                // Fail mapping if all input items have not been consumed.
                return $i < $n ? null: $map;

            // the main diff between sets and sequences is the encapsulation of the foreach in another for loop
            case self::TYPE_SET:
                $map = [];

                // ignore the min and max
                if (isset($mapping['min']) && isset($mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($decoded['content'] as $content) {
                        if (($map[] = self::asn1map($content, $child, $special)) === null) {
                            return null;
                        }
                    }

                    return $map;
                }

                for ($i = 0; $i < count($decoded['content']); $i++) {
                    $currentChild = $decoded['content'][$i];
                    $currentChildClass = self::CLASS_UNIVERSAL;
                    if (isset($currentChild['constant'])) {
                        $currentChildClass = isset($currentChild['class']) ? $currentChild['class'] : self::CLASS_CONTEXT_SPECIFIC;
                    }

                    foreach ($mapping['children'] as $key => $child) {
                        if (isset($map[$key])) {
                            continue;
                        }
                        $mayMatch = true;
                        if ($child['type'] != self::TYPE_CHOICE) {
                            $childClass = self::CLASS_UNIVERSAL;
                            $constant = null;
                            if (isset($child['class'])) {
                                $childClass = $child['class'];
                                $constant = $child['cast'];
                            } elseif (isset($child['constant'])) {
                                $childClass = self::CLASS_CONTEXT_SPECIFIC;
                                $constant = $child['constant'];
                            }

                            if (isset($constant) && isset($currentChild['constant'])) {
                                // Can only match if constants and class match.
                                $mayMatch = $constant == $currentChild['constant'] && $childClass == $currentChildClass;
                            } else {
                                // Can only match if no constant expected and type matches or is generic.
                                $mayMatch = !isset($child['constant']) && array_search($child['type'], [$currentChild['type'], self::TYPE_ANY, self::TYPE_CHOICE]) !== false;
                            }
                        }

                        if ($mayMatch) {
                            // Attempt submapping.
                            $candidate = self::asn1map($currentChild, $child, $special);
                            $mayMatch = $candidate !== null;
                        }

                        if (!$mayMatch) {
                            break;
                        }

                        // Got the match: use it.
                        if (isset($special[$key])) {
                            $candidate = call_user_func($special[$key], $candidate);
                        }
                        $map[$key] = $candidate;
                        break;
                    }
                }

                foreach ($mapping['children'] as $key => $child) {
                    if (!isset($map[$key])) {
                        if (isset($child['default'])) {
                            $map[$key] = $child['default'];
                        } elseif (!isset($child['optional'])) {
                            return null;
                        }
                    }
                }
                return $map;
            case self::TYPE_OBJECT_IDENTIFIER:
                return isset(self::$oids[$decoded['content']]) ? self::$oids[$decoded['content']] : $decoded['content'];
            case self::TYPE_UTC_TIME:
            case self::TYPE_GENERALIZED_TIME:
                if (isset($mapping['implicit'])) {
                    $decoded['content'] = self::decodeTime($decoded['content'], $decoded['type']);
                }
                return @date(self::$format, $decoded['content']);
            case self::TYPE_BIT_STRING:
                if (isset($mapping['mapping'])) {
                    $offset = ord($decoded['content'][0]);
                    $size = (strlen($decoded['content']) - 1) * 8 - $offset;
                    /*
                       From X.680-0207.pdf#page=46 (21.7):

                       "When a "NamedBitList" is used in defining a bitstring type ASN.1 encoding rules are free to add (or remove)
                        arbitrarily any trailing 0 bits to (or from) values that are being encoded or decoded. Application designers should
                        therefore ensure that different semantics are not associated with such values which differ only in the number of trailing
                        0 bits."
                    */
                    $bits = count($mapping['mapping']) == $size ? [] : array_fill(0, count($mapping['mapping']) - $size, false);
                    for ($i = strlen($decoded['content']) - 1; $i > 0; $i--) {
                        $current = ord($decoded['content'][$i]);
                        for ($j = $offset; $j < 8; $j++) {
                            $bits[] = (bool) ($current & (1 << $j));
                        }
                        $offset = 0;
                    }
                    $values = [];
                    $map = array_reverse($mapping['mapping']);
                    foreach ($map as $i => $value) {
                        if ($bits[$i]) {
                            $values[] = $value;
                        }
                    }
                    return $values;
                }
            case self::TYPE_OCTET_STRING:
                return $decoded['content'];
            case self::TYPE_NULL:
                return '';
            case self::TYPE_BOOLEAN:
                return $decoded['content'];
            case self::TYPE_NUMERIC_STRING:
            case self::TYPE_PRINTABLE_STRING:
            case self::TYPE_TELETEX_STRING:
            case self::TYPE_VIDEOTEX_STRING:
            case self::TYPE_IA5_STRING:
            case self::TYPE_GRAPHIC_STRING:
            case self::TYPE_VISIBLE_STRING:
            case self::TYPE_GENERAL_STRING:
            case self::TYPE_UNIVERSAL_STRING:
            case self::TYPE_UTF8_STRING:
            case self::TYPE_BMP_STRING:
                return $decoded['content'];
            case self::TYPE_INTEGER:
            case self::TYPE_ENUMERATED:
                $currentChild = $decoded['content'];
                if (isset($mapping['implicit'])) {
                    $currentChild = new BigInteger($decoded['content'], -256);
                }
                if (isset($mapping['mapping'])) {
                    $currentChild = (int) $currentChild->toString();
                    return isset($mapping['mapping'][$currentChild]) ?
                        $mapping['mapping'][$currentChild] :
                        false;
                }
                return $currentChild;
        }
    }
}