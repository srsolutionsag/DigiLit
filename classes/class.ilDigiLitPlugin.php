<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\DIC\DigiLit\DICTrait;
use srag\Plugins\DigiLit\Menu\Menu;

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
    use DICTrait;
	const PLUGIN_ID = 'xdgl';
	const PLUGIN_NAME = 'DigiLit';
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
	 * @var ilDB
	 */
	protected $db;


	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return bool
	 */
	protected function uninstallCustom() {
		$this->db->dropTable(xdglConfig::TABLE_NAME, false);
		$this->db->dropTable(xdglLibrarian::TABLE_NAME, false);
		$this->db->dropTable(xdglLibrary::TABLE_NAME, false);
		$this->db->dropTable(xdglRequest::TABLE_NAME, false);
		$this->db->dropTable(xdglRequestUsage::TABLE_NAME, false);

		return true;
	}


	/**
	 * @return bool
	 */
	public function allowCopy() {
		return false;
	}


    /**
     * @inheritDoc
     */
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider {
        return new Menu(self::dic()->dic(), $this);
    }
}
