<?php
/**
 * OCSPRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ASN1Object;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Mapper\Mapper;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class OCSPRequest
 *
 * @see     Maps\OCSPRequest
 * @package Adapik\CMS
 */
class OCSPRequest extends RequestModel
{
    const CONTENT_TYPE = 'application/ocsp-request';
    const OCSP_DEFAULT_NONCE_LENGTH = 16;
    const OID_OCSPNonce = '1.3.6.1.5.5.7.48.1.2';

    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * OCSPRequest constructor.
     *
     * @param Sequence $object
     */
    public function __construct(Sequence $object)
    {
        $this->sequence = $object;
    }

    /**
     * @param Certificate $publicCertificate
     * @param Certificate $intermediateCertificate
     * @param string $hashAlgorithmOID
     *
     * @return OCSPRequest
     * @throws FormatException
     */
    public static function createSimple(Certificate $publicCertificate, Certificate $intermediateCertificate, string $hashAlgorithmOID = Algorithm::OID_SHA1)
    {
        /** @see Maps\TBSRequest */
        $tbsRequest = Sequence::create([
                // потомки TBSRequest

                # version

                # requestorName

                # requestList
                Sequence::create([

                        /** @see Request */

                        Sequence::create([
                                // потомки Request
                                # reqCert

                                /** @see CertID */

                                Sequence::create([
                                        // потомки  CertID

                                        # hashAlgorithm

                                        /** @see AlgorithmIdentifier */

                                        Sequence::create([
                                                // потомки AlgorithmIdentifier

                                                # algorithm
                                                ObjectIdentifier::create($hashAlgorithmOID),

                                                # parameters

                                                NullObject::create(),
                                            ]
                                        ),

                                        # issuerNameHash
                                        OctetString::createFromString($intermediateCertificate->getNameHash($hashAlgorithmOID)),

                                        # issuerKeyHash
                                        OctetString::createFromString($intermediateCertificate->getKeyHash($hashAlgorithmOID)),

                                        # serialNumber

                                        Integer::create($publicCertificate->getSerial()),
                                    ]
                                ),
                                # singleRequestExtensions
                            ]
                        ),
                    ]
                ),
                # requestExtensions
                ExplicitlyTaggedObject::create(2,
                    Sequence::create([
                            Sequence::create([
                                ObjectIdentifier::create(self::OID_OCSPNonce),
                                OctetString::createFromString(OctetString::createFromString((string)self::generateNonce())->getBinary()),
                            ],
                            ),
                        ]
                    ),
                ),
            ]
        );

        return new self(Sequence::create([$tbsRequest/*, $optionalSignature*/]));
    }

    /**
     * @param int|null $length
     * @return string
     * @throws Exception
     */
    private static function generateNonce(int $length = null)
    {
        return random_bytes($length ?? self::OCSP_DEFAULT_NONCE_LENGTH);
    }

    /**
     * @param $content
     *
     * @return OCSPRequest
     * @throws FormatException
     */
    public static function createFromContent($content)
    {
        $sequence = ASN1Object::fromFile($content);

        if (!$sequence instanceof Sequence) {
            throw new FormatException('OCSPRequest must be type of Sequence');
        }

        $map = (new Mapper())->map($sequence, Maps\OCSPRequest::MAP);

        if ($map === null) {
            throw new FormatException('OCSPRequest invalid format');
        }

        return new self($sequence);
    }

    /**
     * @param string[] $urls
     * @param int $timeOut
     * @return OCSPResponse|null
     * @throws FormatException
     */
    public function processRequest(array $urls, int $timeOut = 5)
    {
        $this->processErrors = [];

        foreach ($urls as $url) {

            $result = $this->curlRequest($url, $this->sequence->getBinary(), self::CONTENT_TYPE, OCSPResponse::CONTENT_TYPE);

            // Actually we need only 1 response, and if array is not set - we do not have any errors
            if (!isset($this->processErrors[$url]) && !is_null($result)) {
                return OCSPResponse::createFromContent($result);
            }
        }

        return null;
    }

    /**
     * @return TBSRequest
     * @throws FormatException
     */
    public function getTBSRequest()
    {
        $tbsRequest = $this->sequence->getChildren()[0];

        return TBSRequest::createFromContent($tbsRequest->getBinary());
    }

    /**
     * @return Signature|null
     * @throws FormatException
     */
    public function getOptionalSignature()
    {
        $children = $this->sequence->getChildren();

        if (count($children) == 2) {
            return Signature::createFromContent($children[1]->getBinaryContent());
        }

        return null;
    }
}