<?php
/**
 * MessageImprintTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\MessageImprint;
use Adapik\CMS\SignedData;
use Adapik\CMS\TimeStampToken;
use PHPUnit\Framework\TestCase;

class MessageImprintTest extends TestCase
{
    public function testBase()
    {
        $signedData = SignedData::createFromContent(file_get_contents(__DIR__ . '/../fixtures/pull_request.cms'));
        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            /** @var TimeStampToken $timeStampToken */
            $timeStampToken = $unsignedAttributes->getTimeStampToken();
            if (!is_null($timeStampToken)) {
                $messageImprint = $timeStampToken->getTSTInfo()->getMessageImprint();

                $hashAlgorithm = $messageImprint->getHashAlgorithm();
                self::assertEquals("2.16.840.1.101.3.4.2.1", (string)$hashAlgorithm->getAlgorithmOid());

                $hashedMessage = $messageImprint->getHashedMessage();
                self::assertEquals("4b46d844187bdc58f8da296fac53ceb1013135cd64204a08b124723f132ca325", bin2hex($hashedMessage->getBinaryContent()));

                $binary = $messageImprint->getBinary();
                $newMessageImprint = MessageImprint::createFromContent($binary);
                self::assertEquals($binary, $newMessageImprint->getBinary());
            }
        }
    }
}
