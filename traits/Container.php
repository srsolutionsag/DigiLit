<?php

namespace xdgl;

use ilCtrl;
use ilLanguage;
use ilObjUser;
use ilTabsGUI;
use ilTree;

/**
 * Interface Container
 *
 * @package xdgl
 */
interface Container extends \ArrayAccess {

	/**
	 * @return ilCtrl
	 */
	public function ctrl();


	/**
	 * @return ilObjUser
	 */
	public function user();


	/**
	 * @return LegacyUI
	 */
	public function ui();


	/**
	 * @return ilTabsGUI
	 */
	public function tabs();


	/**
	 * @return ilLanguage
	 */
	public function language();


	/**
	 * @return ilTree
	 */
	public function repositoryTree();
}
