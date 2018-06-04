<?php

namespace xdgl;

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
