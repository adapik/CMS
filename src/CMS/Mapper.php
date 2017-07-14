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
            if (array_key_exists('constant', $mapping) && $mapping['constant'] === $object->getIdentifier()->getTagNumber()) {
                $object = $object->getChildren()[0];
            } else {
                return null;
            }
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

                // if min and max are present - its sequence of
                //add all elements
                if (isset($mapping['min'], $mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($object->getChildren() as $content) {
                        if (($map[] = self::map($content, $child)) === null) {
                            return null;
                        }
                    }

                    return $map;
                }

                $childrenCount = count($object->getChildren());
                for ($i = 0; $i < $childrenCount; $i++) {
                    $currentChild   = $object->getChildren()[$i];
                    $currentMapping = reset($mapping['children']);
                    $currentKey = key($mapping['children']);
                    $matched = self::map($currentChild, $currentMapping);
                    if ($matched) {
                        $map[$currentKey] = $matched = self::map($currentChild, $currentMapping);
                        array_shift($mapping['children']);
                    }

                    while (!$matched && array_key_exists('optional', $currentMapping) && $currentMapping['optional'] === true) {
                        array_shift($mapping['children']);
                        $currentMapping = reset($mapping['children']);
                        $matched = self::map($currentChild, $currentMapping);
                        if ($matched) {
                            $map[$currentKey] = $matched = self::map($currentChild, $currentMapping);
                            array_shift($mapping['children']);
                            break;
                        }
                    }
                }

                $unprocessedMappings = array_filter($mapping['children'], function($map) {
                    return !array_key_exists('optional', $map);
                });


                if(count($unprocessedMappings) > 0) {
                    return null;
                }

                return $map;
            break;
            // the main diff between sets and sequences is the encapsulation of the foreach in another for loop
            case Identifier::SET:
                $map = [];

                // if min and max are present - its sequence of
                //add all elements
                if (isset($mapping['min'], $mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($object->getChildren() as $content) {
                        if (($map[] = self::map($content, $child)) === null) {
                            return null;
                        }
                    }

                    return $map;
                }

                $childrenCount = count($object->getChildren());
                for ($i = 0; $i < $childrenCount; $i++) {
                    $currentChild = $object->getChildren()[$i];
                    foreach ($mapping['children'] as $key => $childMapping) {
                        $matched = self::map($currentChild, $childMapping);
                        if ($matched) {
                            $map[$key] = $matched;
                            unset($mapping['children'][$key]);
                            break;
                        }
                    }
                }

                $unprocessedMappings = array_filter($mapping['children'], function($map) {
                    return !array_key_exists('optional', $map);
                });


                if(count($unprocessedMappings) > 0) {
                    return null;
                }

                return $map;
            default:
                return $object;
        }
    }
}