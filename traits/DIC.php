<?php

namespace xdgl;

/**
 * Class DIC
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
trait DIC {

	/**
	 * @return Container
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
	 * @return UIServices
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

/**
 * Class LegacyDIC
 *
 * @package xdgl
 */
class LegacyDIC implements Container {

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset) {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset) {
		switch ($offset) {
		case 'tree':
			return $GLOBALS['tree'];
		}
	}


	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value) {
		// TODO: Implement offsetSet() method.
	}


	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset) {
		// TODO: Implement offsetUnset() method.
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl() {
		return $GLOBALS['ilCtrl'];
	}


	/**
	 * @inheritdoc
	 */
	public function user() {
		return $GLOBALS['ilUser'];
	}


	/**
	 * @inheritdoc
	 */
	public function ui() {
		return new LegacyUI();
	}


	/**
	 * @inheritdoc
	 */
	public function tabs() {
		return $GLOBALS['ilTabs'];
	}


	/**
	 * @inheritdoc
	 */
	public function language() {
		return $GLOBALS['lng'];
	}


	/**
	 * @inheritdoc
	 */
	public function repositoryTree() {
		return $GLOBALS['tree'];
	}
}

/**
 * Class LegacyUI
 *
 * @package xdgl
 */
class LegacyUI implements UIServices {

	/**
	 * @inheritdoc
	 */
	public function mainTemplate() {
		return $GLOBALS['tpl'];
	}
}

/**
 * Interface UIServices
 *
 * @package xdgl
 */
interface UIServices {

	/**
	 * @return \ilTemplate
	 */
	public function mainTemplate();
}

/**
 * Interface Container
 *
 * @package xdgl
 */
interface Container extends \ArrayAccess {

	/**
	 * @return \ilCtrl
	 */
	public function ctrl();


	/**
	 * @return \ilObjUser
	 */
	public function user();


	/**
	 * @return LegacyUI
	 */
	public function ui();


	/**
	 * @return \ilTabsGUI
	 */
	public function tabs();


	/**
	 * @return \ilLanguage
	 */
	public function language();


	/**
	 * @return ilTree
	 */
	public function repositoryTree();
}