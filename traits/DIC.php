<?php

namespace xdgl;

/**
 * Class DIC
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

trait DIC {

	/**
	 * @return \ILIAS\DI\Container|LegacyDIC
	 */
	public function dic() {
		if (!is_object($GLOBALS['DIC'])) {
			$GLOBALS['DIC'] = new LegacyDIC();
		}

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
		return $this->dic()->repositoryTree();
	}
}

class LegacyDIC implements \ArrayAccess {

	public function offsetExists($offset) {
		return true;
	}


	public function offsetGet($offset) {
		switch ($offset) {
		case 'tree':
			return $GLOBALS['tree'];
		}
	}


	public function offsetSet($offset, $value) {
		// TODO: Implement offsetSet() method.
	}


	public function offsetUnset($offset) {
		// TODO: Implement offsetUnset() method.
	}


	public function ctrl() {
		return $GLOBALS['ilCtrl'];
	}


	public function user() {
		return $GLOBALS['ilUser'];
	}


	public function ui() {
		return new LegacyUI();
	}


	public function tabs() {
		return $GLOBALS['ilTabs'];
	}


	public function language() {
		return $GLOBALS['lng'];
	}


	public function repositoryTree() {
		return $GLOBALS['tree'];
	}
}

class LegacyUI {

	/**
	 * @return \ilTemplate
	 */
	public function mainTemplate() {
		return $GLOBALS['tpl'];
	}
}