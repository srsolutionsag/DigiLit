<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/vendor/autoload.php');

/**
 * Class xdglRequestUsage
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

class xdglRequestUsage extends ActiveRecord implements xdglRequestUsageInterface {

	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}

	/**
	 * @var int
	 *
	 * @db_has_field          true
	 * @db_fieldtype          integer
	 * @db_length             4
	 * @db_sequence           true
	 * @db_is_primary         true
	 */
	protected $id;

	/**
	 * @var int
	 *
	 * @db_has_field          true
	 * @db_fieldtype          integer
	 * @db_length             4
	 */
	protected $request_id;

	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $crs_ref_id;

	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           4
	 */
	protected $obj_id;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getRequestId() {
		return $this->request_id;
	}


	/**
	 * @param int $request_id
	 */
	public function setRequestId($request_id) {
		$this->request_id = $request_id;
	}


	/**
	 * @return string
	 */
	public function getCrsRefId() {
		return $this->crs_ref_id;
	}


	/**
	 * @param string $crs_ref_id
	 */
	public function setCrsRefId($crs_ref_id) {
		$this->crs_ref_id = $crs_ref_id;
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}



}