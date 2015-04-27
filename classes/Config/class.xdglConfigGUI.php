<?php
require_once('class.xdglConfigFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibraryGUI.php');

/**
 * Class xdglConfigGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.00
 *
 * @ilCtrl_isCalledBy xdglConfigGUI : xdglMainGUI, ilObjComponentSettingsGUI
 */
class xdglConfigGUI {

	const CMD_STANDARD = 'configure';
	const CMD_INDEX = 'index';
	const CMD_CANCEL = 'cancel';
	const CMD_SAVE = 'save';
	const TAB_SETTINGS = 'settings';
	const TAB_LIBRARIES = 'libraries';
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs_gui;
	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;
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
		$this->tabs_gui = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
	}


	/**
	 * @return bool
	 */
	public function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			case self::CMD_STANDARD:
			case self::CMD_INDEX:
			case self::CMD_CANCEL:
			case '':
				$this->index();
				break;
			case self::CMD_SAVE:
				$this->$cmd();
				break;
		}
	}


	public function index() {
		if (!ilObjDigiLitAccess::isAdmin()) {
			return false;
		}
		$form = new xdglConfigFormGUI($this);
		$form->fillForm();
		$this->tpl->setContent($form->getHTML());
	}


	protected function save() {
		if (!ilObjDigiLitAccess::isAdmin()) {
			return false;
		}
		$form = new xdglConfigFormGUI($this);
		$form->setValuesByPost();
		if ($form->saveObject()) {
			ilUtil::sendSuccess($this->pl->txt('msg_success_add'), true);
			$this->ctrl->redirect($this, self::CMD_INDEX);
		}
		$this->tpl->setContent($form->getHTML());
	}
}

?>
