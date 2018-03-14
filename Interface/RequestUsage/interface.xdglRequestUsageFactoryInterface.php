<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/vendor/autoload.php');

/**
 * Class xdglRequestUsageFactoryInterface
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

interface xdglRequestUsageFactoryInterface {

	/**
	 * @param $obj_id int
	 *
	 * @return xdglRequestUsage | null
	 */
	public function getInstanceByObjectId($obj_id);


	/**
	 * @param xdglRequest $xdglRequest
	 * @param ilObjDigiLit $ilObjDigiLit
	 *
	 * @return xdglRequestUsage
	 */
	public function createRequestUsageFromRequestAndDigiLitObject(xdglRequest $xdglRequest, ilObjDigiLit $ilObjDigiLit);


	/**
	 * @param int $request_id
	 *
	 * @return array of xdglRequestUsage | null
	 */
	public function getRequestUsagesByRequestId($request_id);

}