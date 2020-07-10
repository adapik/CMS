<?php
/**
 * MessageImprint
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class MessageImprint
{
	const MAP = [
		'type' => Identifier::SEQUENCE,
		'children' => [
			'hashAlgorithm' => AlgorithmIdentifier::MAP,
			'hashedMessage' => ['type' => Identifier::OCTETSTRING]
		],
	];
}