<?php
/**
 * CMSBaseTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\SignedData;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

class CMSBaseTest extends TestCase
{
    public function testBase()
    {
        $binary = base64_decode(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));
        $signedData = SignedData::createFromContent($binary);

        self::assertNotNull($signedData->getBinaryContent());
        self::assertNotNull($signedData->getBase64());
        self::assertNotNull($signedData->getBase64Content());
        self::assertNotNull($signedData->getBase64(false));
        self::assertNotNull($signedData->getBase64Content(false));
    }

    public function testFailure()
    {
        $binary = base64_decode(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));

        self::expectException(FormatException::class);
        Bad::createFromContent($binary);
    }
}

class Bad extends CMSBase
{
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, MadMap::class, Sequence::class));
    }
}

abstract class MadMap
{
    const MAP = [
        'type' => Identifier::BOOLEAN
    ];
}
