<?php
/**
 * SubjectTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\Certificate;
use PHPUnit\Framework\TestCase;

class SubjectTest extends TestCase
{
    /**
     * @var Certificate
     */
    private $userCert;

    /**
     * @var Certificate
     */
    private $caCert;

    public function testCaCert()
    {
        $subject = $this->caCert->getSubject();

        $value = $subject->getAliasedEntryName();
        self::assertNull($value);

        $value = $subject->getCommonName();
        self::assertEquals("Gandi Standard SSL CA 2", $value);

        $value = $subject->getCountryName();
        self::assertEquals("FR",$value);

        $value = $subject->getDescription();
        self::assertNull($value);

        $value = $subject->getEmailAddress();
        self::assertNull($value);

        $value = $subject->getGivenName();
        self::assertNull($value);

        $value = $subject->getKnowledgeInformation();
        self::assertNull($value);

        $value = $subject->getLocalityName();
        self::assertEquals("Paris",$value);

        $value = $subject->getOrganizationName();
        self::assertEquals("Gandi", $value);

        $value = $subject->getOrganizationalUnitName();
        self::assertNull($value);

        $value = $subject->getSerialNumber();
        self::assertNull($value);

        $value = $subject->getStateOrProvinceName();
        self::assertEquals("Paris",$value);

        $value = $subject->getStreetAddress();
        self::assertNull($value);

        $value = $subject->getSurname();
        self::assertNull($value);

        $value = $subject->getTitle();
        self::assertNull($value);
    }

    public function testUserCert()
    {
        $subject = $this->userCert->getSubject();

        $value = $subject->getAliasedEntryName();
        self::assertNull($value);

        $value = $subject->getCommonName();
        self::assertEquals("*.php.net", $value);

        $value = $subject->getCountryName();
        self::assertNull($value);

        $value = $subject->getDescription();
        self::assertNull($value);

        $value = $subject->getEmailAddress();
        self::assertNull($value);

        $value = $subject->getGivenName();
        self::assertNull($value);

        $value = $subject->getKnowledgeInformation();
        self::assertNull($value);

        $value = $subject->getLocalityName();
        self::assertNull($value);

        $value = $subject->getOrganizationName();
        self::assertNull($value);

        $value = $subject->getOrganizationalUnitName();
        self::assertEquals("Domain Control Validated", $value);

        $value = $subject->getSerialNumber();
        self::assertNull($value);

        $value = $subject->getStateOrProvinceName();
        self::assertNull($value);

        $value = $subject->getStreetAddress();
        self::assertNull($value);

        $value = $subject->getSurname();
        self::assertNull($value);

        $value = $subject->getTitle();
        self::assertNull($value);
    }

    protected function setUp(): void
    {
        $this->userCert = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_user.crt')));
        $this->caCert = Certificate::createFromContent(base64_decode(file_get_contents(__DIR__ . '/../fixtures/cert_ca.crt')));
    }
}
