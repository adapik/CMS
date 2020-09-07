<?php
/**
 * ReturnObjectTest
 *
 * Any getFunction should not return ASN1 Object, always instance of CMS object package.
 * We do not allow edit CMS object directly
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\Test\CMS;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;

class ReturnObjectTest extends TestCase
{
    const NAMESPACE = "Adapik\\CMS\\";
    public function testObjectReturn() {
        $classes = $this->getTestClasses(spl_autoload_functions()[0][0]);

        return;
    }

    /**
     * @param ClassLoader $composer
     * @return array
     */
    private function getTestClasses(ClassLoader $composer) {
        // Подгружаем все наши классы
        $classes = array_keys($composer->getClassMap());

        $regexp = "/^" . str_replace('\\', '\\\\', self::NAMESPACE) . "\w+$/";

        /** Фильтруем все классы из наших сорсов. */
        return array_filter($classes,
            function($key) use ($regexp) {
                if(strstr($key, self::NAMESPACE)) {
                    if(preg_match($regexp, $key))
                        return $key;
                }

                return false;
            }
        );
    }
}
