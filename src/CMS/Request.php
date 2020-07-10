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
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Sequence;

/**
 * Class Request
 *
 * @see     Maps\Request
 * @package Adapik\CMS
 */
class Request
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * Request constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param $content
     *
     * @return Request
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('Request must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\Request::MAP);

        if ($map === null) {
            throw new FormatException('Request invalid format');
        }

        return new self($sequence);
    }

    /**
     * @return CertID
     * @throws FormatException
     */
    public function getReqCert()
    {
        $reqCert = $this->sequence->findChildrenByType(Sequence::class)[0];

        return CertID::createFromContent($reqCert->getBinary());
    }

    /**
     * @return ExplicitlyTaggedObject[]
     * @throws Exception
     */
    public function getSingleRequestExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $list */
        $list = $this->sequence->findChildrenByType(ExplicitlyTaggedObject::class);

        return $list;
    }
}
