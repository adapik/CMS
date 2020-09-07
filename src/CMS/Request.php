<?php
/**
 * Request
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Sequence;

/**
 * Class Request
 *
 * @see     Maps\Request
 * @package Adapik\CMS
 */
class Request extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return Request
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\Request::class, Sequence::class));
    }

    /**
     * @return CertID
     * @throws Exception
     */
    public function getReqCert()
    {
        $reqCert = $this->object->findChildrenByType(Sequence::class)[0];

        return new CertID($reqCert);
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ExplicitlyTaggedObject[]
     * @throws Exception
     */
    public function getSingleRequestExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $list */
        $list = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

        return $list;
    }
}
