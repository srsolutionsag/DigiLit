<?php
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


	public function getRequestUsagesByRequestId($request_id) {
		$xdglRequestUsageArray = xdglRequestUsage::where(array('request_id' => $request_id))->getArray();
		return $xdglRequestUsageArray;
	}
}