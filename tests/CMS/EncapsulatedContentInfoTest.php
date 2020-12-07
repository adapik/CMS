<?php
/**
 * EncapsulatedContentInfoTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\EncapsulatedContentInfo;
use Adapik\CMS\SignedData;
use PHPUnit\Framework\TestCase;

class EncapsulatedContentInfoTest extends TestCase
{
    public function testBase()
    {
        $signedData = SignedData::createFromContent(file_get_contents(__DIR__ . '/../fixtures/cms_attached_chain.sig'));
        $encapsulatedContentInfo = $signedData->getSignedDataContent()->getEncapsulatedContentInfo();

        self::assertEquals("1.2.840.113549.1.7.1", $encapsulatedContentInfo->getContentType());

        $binary = $encapsulatedContentInfo->getBinary();
        $newEncapsulatedContentInfo = encapsulatedContentInfo::createFromContent($binary);
        self::assertEquals($binary, $newEncapsulatedContentInfo->getBinary());
    }
}