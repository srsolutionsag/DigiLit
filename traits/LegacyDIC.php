<?php

namespace xdgl;

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