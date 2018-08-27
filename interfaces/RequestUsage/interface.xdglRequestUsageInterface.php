<?php
/**
 * Class xdglRequestUsageInterface
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

interface xdglRequestUsageInterface {

	const TABLE_NAME = 'xdgl_request_usage';


	/**
	 * @return int
	 */
	public function getId();


	/**
	 * @param int $id
	 */
	public function setId($id);


	/**
	 * @return int
	 */
	public function getRequestId();


	/**
	 * @param int $request_id
	 */
	public function setRequestId($request_id);


	/**
	 * @return string
	 */
	public function getCrsRefId();


	/**
	 * @param string $crs_ref_id
	 */
	public function setCrsRefId($crs_ref_id);


	/**
	 * @return int
	 */
	public function getObjId();


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id);
}
