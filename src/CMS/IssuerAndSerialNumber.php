<?php
/**
 * IssuerAndSerialNumber
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;

/**
 * Class IssuerAndSerialNumber
 *
 * @see     Maps\IssuerAndSerialNumber
 * @package Adapik\CMS
 */
class IssuerAndSerialNumber extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return IssuerAndSerialNumber
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, Maps\IssuerAndSerialNumber::class, Sequence::class));
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
        return new Name($this->object->getChildren()[0]);
    }
}
