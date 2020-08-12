<?php
/**
 * SignerInfoInterface
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Interfaces;

use Adapik\CMS\IssuerAndSerialNumber;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\OctetString;

interface SignerInfoInterface
{
	/**
	 * @return IssuerAndSerialNumber|null
	 */
	public function getIssuerAndSerialNumber();

	/**
	 * @return OctetString|null
	 * @throws ParserException
	 */
	public function getSubjectKeyIdentifier();
}
