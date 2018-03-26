<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

$t1  = microtime(true);
$crl = \Adapik\CMS\CertificateRevocationList::createFromContent(file_get_contents('php://stdin'));
$t2  = microtime(true);

echo 'Parsed in: ' . ($t2 - $t1) . 's';
echo "\n";

echo 'Issuer:  ' . (string) $crl->getIssuer();
echo "\n";
echo 'This Update:  ' . (string) $crl->getThisUpdate();
echo "\n";
echo 'Next Update:  ' . (string) $crl->getNextUpdate();
echo "\n";
echo 'Revoked Certificates Count: ' . count($crl->getSerialNumbers());
echo "\n";

$t3 = microtime(true);
echo 'Total: ' . ($t3 - $t1) . 's';
echo "\n";
echo 'Memory Usage: ' . memory_get_peak_usage(true) . ' bytes';
echo "\n";