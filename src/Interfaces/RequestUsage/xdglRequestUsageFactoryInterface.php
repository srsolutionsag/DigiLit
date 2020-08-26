<?php

namespace srag\Plugins\DigiLit\Interfaces\RequestUsage;

use ilObjDigiLit;
use xdglRequest;
use xdglRequestUsage;

/**
 * Interface xdglRequestUsageFactoryInterface
 *
 * @package srag\Plugins\DigiLit\Interfaces\RequestUsage
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
interface xdglRequestUsageFactoryInterface {

	/**
	 * @param int $obj_id
	 *
	 * @return xdglRequestUsage | null
	 */
	public function getInstanceByObjectId($obj_id);


	/**
	 * @param xdglRequest  $xdglRequest
	 * @param ilObjDigiLit $ilObjDigiLit
	 *
	 * @return xdglRequestUsage
	 */
	public function createRequestUsageFromRequestAndDigiLitObject(xdglRequest $xdglRequest, ilObjDigiLit $ilObjDigiLit);


	/**
	 * @param int $request_id
	 *
	 * @return array of xdglRequestUsages | null
	 */
	public function getRequestUsagesByRequestId($request_id);


	/**
	 * @param array $request_usages_array
	 *
	 * @return array of cours titles
	 */
	public function getAllCoursTitlesWithRequestUsages($request_usages_array);


	/**
	 * @param int $request_id
	 */
	public function deleteUsagesAndDigiLitObjectsByRequestId($request_id);


	/**
	 * @param int $obj_id
	 */
	public function deleteRequestUsageAndDigiLitByObjId($obj_id);


	/**
	 * @param int $obj_id
	 */
	public function deleteRequestUsageByObjId($obj_id);


	/**
	 * @param xdglRequestUsage $old_request_usage
	 * @param int              $obj_id
	 *
	 * @return xdglRequestUsage
	 */
	public function copyRequestUsage(xdglRequestUsage $old_request_usage, $obj_id = NULL);
}