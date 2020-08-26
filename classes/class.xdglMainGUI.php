<?php

require_once __DIR__ . '/../vendor/autoload.php';

use srag\DIC\DigiLit\DICTrait;

/**
 * Class xdglMainGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy xdglMainGUI : ilUIPluginRouterGUI, ilUIPluginRouterGUI
 * @ilCtrl_IsCalledBy xdglMainGUI : ilDigiLitConfigGUI
 */
class xdglMainGUI {
    use DICTrait;
	const TAB_SETTINGS = 'settings';
	const TAB_LIBRARIES = 'libraries';
	const TAB_REQUESTS = 'requests';
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
	}


	/**
	 *
	 */
	public function executeCommand() {
	    ilObjDigiLitAccess::hasAccessToMainGUI(true);
		$xdglRequestGUI = new xdglRequestGUI();
		$this->tabs->addTab(self::TAB_REQUESTS, $this->pl->txt('tab_' . self::TAB_REQUESTS), $this->ctrl->getLinkTarget($xdglRequestGUI));
		$xdglLibraryGUI = new xdglLibraryGUI();
		if (ilObjDigiLitAccess::isAdmin()) {
			$xdglConfigGUI = new xdglConfigGUI();
			$this->tabs->addTab(self::TAB_SETTINGS, $this->pl->txt('tab_' . self::TAB_SETTINGS), $this->ctrl->getLinkTarget($xdglConfigGUI));
			if (xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES)) {
				$this->tabs->addTab(self::TAB_LIBRARIES, $this->pl->txt('tab_' . self::TAB_LIBRARIES), $this->ctrl->getLinkTarget($xdglLibraryGUI));
			}
		}
		$nextClass = $this->ctrl->getNextClass();
		if (!xdglConfig::isConfigUpToDate()) {
			ilUtil::sendInfo($this->pl->txt("conf_out_of_date"));
			$nextClass = strtolower(xdglConfigGUI::class);
		}
		global $ilUser;
		if (xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES) AND xdglConfig::getConfigValue(xdglConfig::F_OWN_LIBRARY_ONLY)
			AND !xdglLibrary::isAssignedToAnyLibrary($ilUser)) {
			ilUtil::sendInfo($this->pl->txt('no_library_assigned'), true);
			ilUtil::redirect('/');
		}

		switch ($nextClass) {
			case 'xdglconfiggui';
				$this->tabs->activateTab(self::TAB_SETTINGS);
				$this->ctrl->forwardCommand($xdglConfigGUI);

				break;
			case 'xdgllibrarygui';
				$this->tabs->activateTab(self::TAB_LIBRARIES);
				$this->ctrl->forwardCommand($xdglLibraryGUI);
				break;
			default:
				$this->tabs->activateTab(self::TAB_REQUESTS);
				$this->ctrl->forwardCommand($xdglRequestGUI);

				break;
		}
		if (self::version()->is6()) {
            $this->tpl->loadStandardTemplate();
            $this->tpl->printToStdout();
        } else {
            $this->tpl->getStandardTemplate();
            $this->tpl->show();
        }
	}
}
