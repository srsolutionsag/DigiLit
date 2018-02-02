<?php

namespace xdgl;

/**
 * Class DIC
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

trait DIC {
	/**
	 * @return \ILIAS\DI\Container
	 */
	public function dic() {
		return $GLOBALS['DIC'];
	}
	/**
	 * @return \ilCtrl
	 */
	protected function ctrl() {
		return $this->dic()->ctrl();
	}
	/**
	 * @param $variable
	 *
	 * @return string
	 */
	public function txt($variable) {
		return $this->lng()->txt($variable);
	}
	/**
	 * @return \ilTemplate
	 */
	protected function tpl() {
		return $this->dic()->ui()->mainTemplate();
	}
	/**
	 * @return \ilLanguage
	 */
	protected function lng() {
		return $this->dic()->language();
	}
	/**
	 * @return \ilTabsGUI
	 */
	protected function tabs() {
		return $this->dic()->tabs();
	}
	/**
	 * @return \ILIAS\DI\UIServices
	 */
	protected function ui() {
		return $this->dic()->ui();
	}
	/**
	 * @return \ilObjUser
	 */
	protected function user() {
		return $this->dic()->user();
	}

	protected function tree() {
		return $this->dic()["tree"];
	}
}