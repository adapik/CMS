<?php
/**
 * TBSCertificate
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;

/**
 * Class TBSCertificate
 *
 * @see     Maps\TBSCertificate
 * @package Adapik\CMS
 */
class TBSCertificate extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return static
     * @throws Exception
     */
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\TBSCertificate::class, Sequence::class));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSerialNumber(): string
    {
        return (string)$this->object->findChildrenByType(Integer::class)[0];
    }

    /**
     * @return Name
     */
    public function getIssuer(): Name
    {
        return new Name($this->object->getChildren()[3]);
    }

    /**
     * @return string
     */
    public function getValidNotBefore(): string
    {
        return (string)$this->object->getChildren()[4]->getChildren()[0];
    }

    /**
     * @return string
     */
    public function getValidNotAfter(): string
    {
        return (string)$this->object->getChildren()[4]->getChildren()[1];
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject
    {
        return new Subject($this->object->getChildren()[5]);
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return new PublicKey($this->object->getChildren()[6]);
    }

    /**
     * @return Extension[]
     * @throws Exception
     */
    public function getExtensions(): array
    {
        $exTaggedObjects = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        $filter = array_filter($exTaggedObjects, function (ASN1Object $value) {
            return $value->getIdentifier()->getTagNumber() === 3;
        });

        $return = [];
        $extensions = array_pop($filter);
        /** @var ASN1Object $child */
        foreach ($extensions->getChildren()[0]->getChildren() as $child) {
            $return[] = Extension::createFromContent($child->getBinary());
        }

        return $return;
    }
}
