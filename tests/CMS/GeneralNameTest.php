<?php
/**
 * GeneralNameTest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

declare(strict_types=1);

namespace CMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\GeneralName;
use PHPUnit\Framework\TestCase;

class GeneralNameTest extends TestCase
{
    /**
     * Nothing to test cause not used in this package
     * @throws FormatException
     */
    public function testBase()
    {
        $content = "pIHVMIHSMSQwIgYDVQQDDBvQnNCj0KXQkNCd0J7QkiDQndCj0KDQm9CQ0J0xFzAVBgNVBAQMDtCc0KPQpdCQ0J3QntCSMRgwFgYDVQQFEw9JSU44MDA0MjIzMDAwMTExCzAJBgNVBAYTAktaMRUwEwYDVQQHDAzQkNCb0JzQkNCi0KsxFTATBgNVBAgMDNCQ0JvQnNCQ0KLQqzEbMBkGA1UEKgwS0JzQo9Cg0JDQotCe0JLQmNCnMR8wHQYJKoZIhvcNAQkBFhBOVVJJS0VAR01BSUwuQ09N";

        self::assertInstanceOf(GeneralName::class, GeneralName::createFromContent($content));
    }
}
