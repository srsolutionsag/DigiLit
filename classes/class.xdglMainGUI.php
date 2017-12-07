<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/vendor/autoload.php');

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
	 * @return bool
	 */
	public function executeCommand() {
		$xdglRequestGUI = new xdglRequestGUI();
		$this->tabs->addTab(self::TAB_REQUESTS, $this->pl->txt('tab_' . self::TAB_REQUESTS), $this->ctrl->getLinkTarget($xdglRequestGUI));
		$xdglLibraryGUI = new xdglLibraryGUI();
		if (ilObjDigiLitAccess::isAdmin()) {
			$xdglConfigGUI = new xdglConfigGUI();
			$this->tabs->addTab(self::TAB_SETTINGS, $this->pl->txt('tab_' . self::TAB_SETTINGS), $this->ctrl->getLinkTarget($xdglConfigGUI));
			if (xdglConfig::get(xdglConfig::F_USE_LIBRARIES)) {
				$this->tabs->addTab(self::TAB_LIBRARIES, $this->pl->txt('tab_' . self::TAB_LIBRARIES), $this->ctrl->getLinkTarget($xdglLibraryGUI));
			}
		}
		$nextClass = $this->ctrl->getNextClass();
		if (!xdglConfig::isConfigUpToDate()) {
			ilUtil::sendInfo('Configuraion out of date');
			$nextClass = 'xdglconfiggui';
		}
		global $ilUser;
		if (xdglConfig::get(xdglConfig::F_USE_LIBRARIES) AND xdglConfig::get(xdglConfig::F_OWN_LIBRARY_ONLY)
		                                                     AND !xdglLibrary::isAssignedToAnyLibrary($ilUser)) {
			ilUtil::sendInfo('You cannot use DigiLit since you are not assigned to any Library', true);
			ilUtil::redirect('/');
		}

		switch ($nextClass) {
			case 'xdglconfiggui';
				$this->tabs->setTabActive(self::TAB_SETTINGS);
				$this->ctrl->forwardCommand($xdglConfigGUI);

				break;
			case 'xdgllibrarygui';
				$this->tabs->setTabActive(self::TAB_LIBRARIES);
				$this->ctrl->forwardCommand($xdglLibraryGUI);
				break;
			default:
				$this->tabs->setTabActive(self::TAB_REQUESTS);
				$this->ctrl->forwardCommand($xdglRequestGUI);

				break;
		}
		if (xdglConfig::is50()) {
			$this->tpl->getStandardTemplate();
			$this->tpl->show();
		}
	}
}


