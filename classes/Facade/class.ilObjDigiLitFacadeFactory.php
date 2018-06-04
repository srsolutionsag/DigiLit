<?php


/**
 * Class ilObjDigiLitFacadeFactory
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

class ilObjDigiLitFacadeFactory implements ilObjDigiLitFacadeInterface {

	/**
	 * @var xdglRequestUsageFactory
	 */
	protected $request_usage_factory;

	/**
	 * ilObjDigiLitFacadeFactory constructor.
	 */
	public function __construct() {
		$this->request_usage_factory = new xdglRequestUsageFactory();
	}

	/**
	 * @inheritdoc
	 */
	public function requestUsageFactory() {
		return $this->request_usage_factory;
	}
}