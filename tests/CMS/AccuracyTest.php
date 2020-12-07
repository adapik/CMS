<?php
/**
 * AccuracyTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\Accuracy;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\SignedData;
use Adapik\CMS\TimeStampToken;
use FG\ASN1\Universal\Integer;
use PHPUnit\Framework\TestCase;

class AccuracyTest extends TestCase
{
    public function testCreationAndAccuracy()
    {
        $signedData = $this->getCMS();
        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            /** @var TimeStampToken $timeStampToken */
            $timeStampToken = $unsignedAttributes->getTimeStampToken();
            if (!is_null($timeStampToken)) {
                $accuracy = $timeStampToken->getTSTInfo()->getAccuracy();

                self::assertNotNull($accuracy);
                self::assertInstanceOf(Accuracy::class, $accuracy);
                self::assertInstanceOf(Integer::class, $accuracy->getSeconds());


                $binary = $accuracy->getBinary();

                $newAccuracy = Accuracy::createFromContent($binary);

                self::assertEquals($binary, $newAccuracy->getBinary());
            }
        }
    }

    /**
     * @return SignedData
     * @throws FormatException
     */
    protected function getCMS(): SignedData
    {
        $binary = base64_decode(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));
        return SignedData::createFromContent($binary);
    }
}
