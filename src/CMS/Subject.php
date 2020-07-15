<?php
/**
 * Subject
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

/**
 * Class Subject
 *
 * @package Adapik\CMS
 */
class Subject extends Name
{
	const OID_aliasedEntryName       = "2.5.4.1";
	const OID_commonName             = "2.5.4.3";
	const OID_countryName            = "2.5.4.6";
	const OID_description            = "2.5.4.13";
	const OID_emailAddress           = "1.2.840.113549.1.9.1";
	const OID_givenName              = "2.5.4.42";
	const OID_knowledgeInformation   = "2.5.4.2";
	const OID_localityName           = "2.5.4.7";
	const OID_organizationName       = "2.5.4.10";
	const OID_organizationalUnitName = "2.5.4.11";
	const OID_serialNumber           = "2.5.4.5";
	const OID_stateOrProvinceName    = "2.5.4.8";
	const OID_streetAddress          = "2.5.4.9";
	const OID_surname                = "2.5.4.4";
	const OID_title                  = "2.5.4.12";

	/**
	 * @return string|null
	 */
	public function getAliasedEntryName() {
		return $this->getValueByOid(self::OID_aliasedEntryName);
	}

	/**
	 * @return string|null
	 */
	public function getCommonName() {
		return $this->getValueByOid(self::OID_commonName);
	}

	/**
	 * @return string|null
	 */
	public function getCountryName() {
		return $this->getValueByOid(self::OID_countryName);
	}

	/**
	 * @return string|null
	 */
	public function getDescription() {
		return $this->getValueByOid(self::OID_description);
	}

	/**
	 * @return string|null
	 */
	public function getEmailAddress() {
		return $this->getValueByOid(self::OID_emailAddress);
	}

	/**
	 * @return string|null
	 */
	public function getGivenName() {
		return $this->getValueByOid(self::OID_givenName);
	}

	/**
	 * @return string|null
	 */
	public function getKnowledgeInformation() {
		return $this->getValueByOid(self::OID_knowledgeInformation);
	}

	/**
	 * @return string|null
	 */
	public function getLocalityName() {
		return $this->getValueByOid(self::OID_localityName);
	}

	/**
	 * @return string|null
	 */
	public function getOrganizationName() {
		return $this->getValueByOid(self::OID_organizationName);
	}

	/**
	 * @return string|null
	 */
	public function getOrganizationalUnitName() {
		return $this->getValueByOid(self::OID_organizationalUnitName);
	}

	/**
	 * @return string|null
	 */
	public function getSerialNumber() {
		return $this->getValueByOid(self::OID_serialNumber);
	}

	/**
	 * @return string|null
	 */
	public function getStateOrProvinceName() {
		return $this->getValueByOid(self::OID_stateOrProvinceName);
	}

	/**
	 * @return string|null
	 */
	public function getStreetAddress() {
		return $this->getValueByOid(self::OID_streetAddress);
	}

	/**
	 * @return string|null
	 */
	public function getSurname() {
		return $this->getValueByOid(self::OID_surname);
	}

	/**
	 * @return string|null
	 */
	public function getTitle() {
		return $this->getValueByOid(self::OID_title);
	}

	/**
	 * @param string $oid
	 *
	 * @return string|null
	 */
	private function getValueByOid($oid) {
		$identifiers = $this->object->findByOid($oid);
		if(count($identifiers)) {
			return $identifiers[0]->getSiblings()[0]->__toString();
		}

		return null;
	}
}