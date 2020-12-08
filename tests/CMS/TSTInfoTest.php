<?php
/**
 * TSTInfoTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\MessageImprint;
use Adapik\CMS\TimeStampToken;
use Adapik\CMS\TSTInfo;
use PHPUnit\Framework\TestCase;

class TSTInfoTest extends TestCase
{
    public function testBase()
    {
        $timeStampToken = TimeStampToken::createFromContent(file_get_contents(__DIR__ . '/../fixtures/TimeStampToken.pem'));
        $TSTInfo = $timeStampToken->getTSTInfo();

        self::assertEquals("478819238515317942585566397139431171971049590037", $TSTInfo->getSerialNumber());
        self::assertNull($TSTInfo->getAccuracy());
        self::assertEquals("2020-08-09T14:21:38+00:00", $TSTInfo->getGenTime());
        self::assertInstanceOf(MessageImprint::class, $TSTInfo->getMessageImprint());
        self::assertEquals("8582166393893229182", $TSTInfo->getNonce());
        self::assertNull($TSTInfo->getOrdering());
        self::assertEquals("1.2.398.3.3.2.6.2", $TSTInfo->getPolicy()->__toString());
        self::assertNull($TSTInfo->getTsa());

        $binary = $TSTInfo->getBinary();

        $newTSTInfo = TSTInfo::createFromContent($binary);
        self::assertEquals($binary, $newTSTInfo->getBinary());
    }
}
