<?php
/**
 * RequestModel
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use FG\ASN1\ASN1Object;

abstract class RequestModel
{
    /** @var int $requestTimeout */
    public $requestTimeout = 5;

    /**
     * @var array $processErrors List of errors where associative key is TS url
     */
    protected $processErrors = [];

    /**
     * @see https://github.com/mlocati/ocsp
     *
     * @param string[] $urls
     * @return ASN1Object|null
     */
    abstract public function processRequest(array $urls);

    /**
     * @return array
     */
    public function getProcessErrors()
    {
        return $this->processErrors;
    }

    /**
     * // TODO: catch exception
     * @param string $url
     * @param string $binaryContent
     * @param string $requestContentType
     * @param string $responseContentType
     * @return string|null
     */
    protected function curlRequest(string $url, string $binaryContent, string $requestContentType, string $responseContentType)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $binaryContent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: ' . $requestContentType]);
        /** @noinspection PhpDeprecationInspection */
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->requestTimeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] !== 200) {
            $this->processErrors[$url][] = "Unexpected HTTP Status Response: {$info['http_code']}";
        }

        if ($info['content_type'] !== $responseContentType) {
            $this->processErrors[$url][] = "Unexpected Content-Type header: {$info['content_type']}";
        }

        // Actually we need only response, and if array is not set - we do not have any errors
        if (!isset($this->processErrors[$url])) {
            return $result ?? null;
        }

        return null;
    }
}
