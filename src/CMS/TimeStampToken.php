<?php
/**
 * TimeStampToken
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class TimeStampToken
 *
 * @see     Maps\TimeStampToken
 * @package Adapik\CMS
 */
class TimeStampToken extends UnsignedAttribute
{
    protected static $oid = '1.2.840.113549.1.9.16.2.14';

    /**
     * @param string $content
     * @return TimeStampToken
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TimeStampToken::class, Sequence::class));
    }

    /**
     * @return Sequence
     * @throws Exception
     */
    public static function createEmpty()
    {
        return Sequence::create([
                ObjectIdentifier::create(self::getOid()),
                Set::create([NullObject::create()]),
            ]
        );
    }

    /**
     * @return TSTInfo[]
     * @throws FormatException
     * @throws ParserException
     */
    public function getTSTInfo()
    {
        $TSTInfo = [];
        $signedData = $this->getSignedData();

        foreach ($signedData as $data) {
            $EContent = $data->getSignedDataContent()->getEncapsulatedContentInfo()->getEContent();
            $TSTInfo[] = TSTInfo::createFromContent($EContent->getBinaryContent());
        }

        return $TSTInfo;
    }

    /**
     * @return SignedData[]
     */
    public function getSignedData()
    {
        $SignedData = [];
        $children = $this->object->getChildren()[1]->getChildren();
        foreach ($children as $child) {
            $SignedData[] = new SignedData($child);
        }

        return $SignedData;
    }
}
