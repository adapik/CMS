<?php

require_once __DIR__.'/../vendor/autoload.php';

$t1         = microtime(true);
$signedData = \Adapik\CMS\SignedData::createFromContent(file_get_contents('php://stdin'));
$t2         = microtime(true);

echo 'Parsed in:' . ($t2 - $t1) . 's';
echo "\n";

echo 'Signature Type: ' . $signedData->getSignerInfo()[0]->defineType();
echo "\n";

$certificate = $signedData->extractCertificates()[0];

echo 'Certificate Serial: ' . $certificate->getSerial();
echo "\n";
echo 'Certificate subjectKeyIdentifier: ' . $certificate->getSubjectKeyIdentifier();
echo "\n";
echo 'Certificate authorityKeyIdentifier: ' . $certificate->getAuthorityKeyIdentifier();
echo "\n";
echo 'OCSP Uri: ' . implode(',', $certificate->getOcspUris());
echo "\n";

$t3 = microtime(true);
echo 'Total:' . ($t3 - $t1) . 's';
echo "\n";
echo 'Memory Usage:' . memory_get_peak_usage(true) . ' bytes';
echo "\n";