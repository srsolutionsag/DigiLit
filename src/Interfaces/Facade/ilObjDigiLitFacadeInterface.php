<?php

namespace srag\Plugins\DigiLit\Interfaces\Facade;

use srag\Plugins\DigiLit\Interfaces\RequestUsage\xdglRequestUsageFactoryInterface;

/**
 * Interface ilObjDigiLitFacadeInterface
 *
 * @package srag\Plugins\DigiLit\Interfaces\Facade
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
interface ilObjDigiLitFacadeInterface {

	/**
	 * @return xdglRequestUsageFactoryInterface
	 */
	public function requestUsageFactory();

}