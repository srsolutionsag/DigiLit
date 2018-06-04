<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/vendor/autoload.php');

/**
 * Class xdglRequestUsageFactory
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

class xdglRequestUsageFactory implements xdglRequestUsageFactoryInterface {

	/**
	 * @inheritdoc
	 */
	public function getInstanceByObjectId($obj_id) {
		$xdglRequestUsage = xdglRequestUsage::where(array('obj_id' => $obj_id))->first();
		if(empty($xdglRequestUsage)) {
			$xdglRequestUsage = new xdglRequestUsage();
		}
		return $xdglRequestUsage;
	}


	/**
	 * @inheritdoc
	 */
	public function createRequestUsageFromRequestAndDigiLitObject(xdglRequest $xdglRequest, ilObjDigiLit $ilObjDigiLit) {
		$xdglRequestUsage = new xdglRequestUsage();
		$xdglRequestUsage->setObjId($ilObjDigiLit->getId());
		$xdglRequestUsage->setCrsRefId(ilObjDigiLit::returnParentCrsRefId($ilObjDigiLit->getRefId()));
		$xdglRequestUsage->setRequestId($xdglRequest->getId());
		$xdglRequestUsage->create();
		return $xdglRequestUsage;
	}

	/**
	 * @inheritdoc
	 */
	public function getRequestUsagesByRequestId($request_id) {
		$xdglRequestUsagesArray = xdglRequestUsage::where(array('request_id' => $request_id))->get();
		return $xdglRequestUsagesArray;
	}

	/**
	 * @inheritdoc
	 */
	public function getAllCoursTitlesWithRequestUsages($request_usages_array) {
		$crs_titles_array = [];
		foreach($request_usages_array as $key => $data) {
			$crs_obj_id = ilObject2::_lookupObjectId($data->getCrsRefId());
			$crs_titles_array[] = ilObject2::_lookupTitle($crs_obj_id);
		}
		return $crs_titles_array;
	}

	/**
	 * @inheritdoc
	 */
	public function deleteUsagesAndDigiLitObjectsByRequestId($request_id) {
		global $ilDB;
		$xdglRequestUsageArray = $this->getRequestUsagesByRequestId($request_id);
		foreach($xdglRequestUsageArray as $key => $data) {
			$ilDB->manipulate("DELETE FROM object_data WHERE obj_id = "  . $ilDB->quote($data->getObjId(), 'integer'));
		}
		$ilDB->manipulate("DELETE FROM xdgl_request_usage WHERE request_id = "  . $ilDB->quote($request_id, 'integer'));
	}

	/**
	 * @inheritdoc
	 */
	public function deleteRequestUsageAndDigiLitByObjId($obj_id) {
		global $ilDB;
		$ilDB->manipulate("DELETE FROM xdgl_request_usage WHERE obj_id = "  . $ilDB->quote($obj_id, 'integer'));
		$ilDB->manipulate("DELETE FROM object_data WHERE obj_id = "  . $ilDB->quote($obj_id, 'integer'));
	}


	public function deleteRequestUsageByObjId($obj_id) {
		global $ilDB;
		$ilDB->manipulate("DELETE FROM xdgl_request_usage WHERE obj_id = "  . $ilDB->quote($obj_id, 'integer'));
	}


	/**
	 * @inheritdoc
	 */
	public function copyRequestUsage(xdglRequestUsage $old_request_usage, $obj_id = null) {
		$new_request_usage = clone($old_request_usage);
		$new_request_usage->setObjId($obj_id);
		$new_request_usage->setRequestId($old_request_usage->getRequestId());
		$new_request_usage->setCrsRefId($old_request_usage->getCrsRefId());
		$new_request_usage->create();

		return $new_request_usage;
	}
}