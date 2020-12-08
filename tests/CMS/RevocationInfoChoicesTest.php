<?php
/**
 * RevocationInfoChoicesTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\SignedData;
use PHPUnit\Framework\TestCase;

class RevocationInfoChoicesTest extends TestCase
{
    public function testBase() {
        $signedData = SignedData::createFromContent(file_get_contents(__DIR__ . '/../fixtures/setOfUnsignedCMS.cms'));

        $choices = $signedData->getSignedDataContent()->getRevocationInfoChoices();

        self::assertNull($choices);
    }
}
