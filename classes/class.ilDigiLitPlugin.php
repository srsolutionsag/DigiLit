<?php
require_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');

/**
 * DigiLit repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version 1.0.00
 *
 */
class ilDigiLitPlugin extends ilRepositoryObjectPlugin {

	const XDGL = 'xdgl';
	/**
	 * @var ilDigiLitPlugin
	 */
	protected static $cache;


	/**
	 * @return ilDigiLitPlugin
	 */
	public static function getInstance() {
		if (!isset(self::$cache)) {
			self::$cache = new self();
		}

		return self::$cache;
	}


	/**
	 * @return string
	 */
	function getPluginName() {
		return self::getStaticPluginName();
	}


	/**
	 * @return string
	 */
	public static function getStaticPluginName() {
		return 'DigiLit';
	}


	/**
	 * @return string
	 */
	public static function getStaticPluginPrefix() {
		return self::XDGL;
	}


	protected function uninstallCustom() {
		// TODO: Implement uninstallCustom() method.
	}


	//	public function txt($a_var) {
	//		require_once('./Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php');
	//		return sragPluginTranslator::getInstance($this)->active()->write()->txt($a_var);
	//		return parent::txt($a_var); // TODO: Change the autogenerated stub
	//	}
}

