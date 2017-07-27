<?php

namespace Adapik\CMS;

use FG\ASN1\ASN1Object;
use FG\ASN1\Identifier;
use FG\ASN1\ImplicitlyTaggedObject;

class Mapper
{
    public function map(ASN1Object $object, array $mapping)
    {
        if ($this->isTaggedObject($mapping)) {
            return $this->mapTaggedObject($object, $mapping);
        }

        if ($mapping['type'] === Identifier::ANY) {
            return $object;
        }

        if ($mapping['type'] === Identifier::CHOICE) {
            return $this->mapChoiceObject($object, $mapping);
        }

        if ($mapping['type'] !== $object->getIdentifier()->getTagNumber()) {
            return null;
        }

        if ($mapping['type'] === Identifier::SEQUENCE) {
            return $this->mapSequenceObject($object, $mapping);
        }

        if ($mapping['type'] === Identifier::SET) {
            return $this->mapSetObject($object, $mapping);
        }

        return $object;
    }

    private function isTaggedObject(array $mapping)
    {
        return array_key_exists('explicit', $mapping) || array_key_exists('implicit', $mapping);
    }

    private function mapTaggedObject(ASN1Object $object, array $mapping)
    {
        $tagNumber = $object->getIdentifier()->getTagNumber();

        if (!array_key_exists('constant', $mapping) || $mapping['constant'] !== $tagNumber) {
            return null;
        }

        if (array_key_exists('explicit', $mapping) && count($object->getChildren()) === 1) {
            $object = $object->getChildren()[0];
        }

        if (array_key_exists('implicit', $mapping) &&
            array_key_exists('type', $mapping)
        ) {
            $object = $object->getDecoratedObject($mapping['type']);
        }

        unset($mapping['explicit'], $mapping['implicit'], $mapping['constant']);

        return $this->map($object, $mapping);
    }

    private function mapChoiceObject(ASN1Object $object, array $mapping)
    {
        foreach ($mapping['children'] as $option) {
            if ($matched = $this->map($object, $option)) {
                return $matched;
            }
        }

        return null;
    }

    private function mapSequenceObject(ASN1Object $object, $mapping)
    {
        $map = [];

        if (array_key_exists('min', $mapping) && array_key_exists('max', $mapping)) {
            return $this->mapSetOf($object, $mapping);
        }

        foreach ($object->getChildren() as $currentChild) {
            $currentMapping = reset($mapping['children']);
            $currentKey     = key($mapping['children']);
            $matched        = $this->map($currentChild, $currentMapping);
            if (null !== $matched) {
                $map[$currentKey] = $matched;
                array_shift($mapping['children']);
            }

            while (null === $matched && array_key_exists('optional', $currentMapping) && $currentMapping['optional'] === true) {
                array_shift($mapping['children']);
                $currentMapping = reset($mapping['children']);
                $currentKey     = key($mapping['children']);
                $matched        = $this->map($currentChild, $currentMapping);
                if (null !== $matched) {
                    $map[$currentKey] = $matched = $this->map($currentChild, $currentMapping);
                    array_shift($mapping['children']);
                    break;
                }
            }
        }

        $unprocessedMappings = array_filter($mapping['children'], function ($map) {
            return !array_key_exists('optional', $map);
        });


        if (count($unprocessedMappings) > 0) {
            return null;
        }

        return $map;
    }

    private function mapSetObject(ASN1Object $object, array $mapping)
    {
        $map = [];

        if (array_key_exists('min', $mapping) && array_key_exists('max', $mapping)) {
            return $this->mapSetOf($object, $mapping);
        }

        $childrenMapping = $mapping['children'];
        foreach ($object->getChildren() as $child) {
            foreach ($childrenMapping as $key => $childMapping) {
                $matched = $this->map($child, $childMapping);
                if ($matched) {
                    $map[$key] = $matched;
                    unset($childrenMapping[$key]);
                }
            }
        }

        $unprocessedMappings = array_filter($childrenMapping, function ($map) {
            return !array_key_exists('optional', $map);
        });


        if (count($unprocessedMappings) > 0) {
            return null;
        }

        return $map;
    }

    private function mapSetOf(ASN1Object $object, array $mapping)
    {
        $map = [];

        $childMapping = $mapping['children'];
        foreach ($object->getChildren() as $childObject) {
            $matched = $this->map($childObject, $childMapping);
            if ($matched === null) {
                return null;
            }

            $map[] = $matched;
        }

        return $map;
    }
}