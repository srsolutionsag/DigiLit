<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/
require_once('./Services/Repository/classes/class.ilObjectPluginGUI.php');
require_once('class.ilDigiLitPlugin.php');
require_once('Request/class.xdglRequestFormGUI.php');
require_once('Request/class.xdglRequest.php');
require_once('./Services/Link/classes/class.ilLink.php');
require_once('./Services/InfoScreen/classes/class.ilInfoScreenGUI.php');
require_once('class.ilObjDigiLit.php');
require_once('./Services/InfoScreen/classes/class.ilInfoScreenGUI.php');
require_once('./Services/Repository/classes/class.ilRepUtilGUI.php');

/**
 * User Interface class for example repository object.
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version           1.0.00
 *
 * Integration into control structure:
 * - The GUI class is called by ilRepositoryGUI
 * - GUI classes used by this class are ilPermissionGUI (provides the rbac
 *   screens) and ilInfoScreenGUI (handles the info screen).
 *
 * @ilCtrl_isCalledBy ilObjDigiLitGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjDigiLitGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 *
 */
class ilObjDigiLitGUI extends ilObjectPluginGUI {

	const CMD_CONFIRM_DELETE_OBJECT = 'confirmDeleteObject';
	const CMD_REDIRECT_PARENT_GUI = 'redirectParentGui';
	const CMD_SHOW_CONTENT = 'showContent';
	const CMD_SEND_FILE = 'sendFile';
	const CMD_DELETE_DIGI_LIT = 'confirmedDelete';
	/**
	 * @var ilObjDigiLit
	 */
	public $object;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;
	/**
	 * @var ilNavigationHistory
	 */
	protected $history;
	/**
	 * @var ilTabsGUI
	 */
	public $tabs_gui;
	/**
	 * @var ilAccessHandler
	 */
	protected $access;


	protected function afterConstructor() {
		global $tpl, $ilCtrl, $ilAccess, $ilNavigationHistory, $ilTabs;
		/**
		 * @var $tpl                 ilTemplate
		 * @var $ilCtrl              ilCtrl
		 * @var $ilAccess            ilAccessHandler
		 * @var $ilNavigationHistory ilNavigationHistory
		 */
		$this->tpl = $tpl;
		$this->history = $ilNavigationHistory;
		$this->access = $ilAccess;
		$this->ctrl = $ilCtrl;
		$this->tabs_gui = $ilTabs;
		$this->pl = ilDigiLitPlugin::getInstance();
	}


	/**
	 * @param ilObjDigiLit $newObj
	 * @param              $additional_args
	 */
	public function afterSave(ilObjDigiLit $newObj, $additional_args) {
		$request = new xdglRequest($additional_args[0]);
		$request->setDigiLitObjectId($newObj->getId());
		$request->update();

		parent::afterSave($newObj);
	}


	/**
	 * @return string
	 */
	final function getType() {
		return ilDigiLitPlugin::XDGL;
	}


	public function executeCommand() {
		if ($this->access->checkAccess('read', '', $_GET['ref_id'])) {
			$this->history->addItem($_GET['ref_id'], $this->ctrl->getLinkTarget($this, $this->getStandardCmd()), $this->getType(), '');
		}
		$cmd = $this->ctrl->getCmd();
		$next_class = $this->ctrl->getNextClass($this);
		$this->tpl->getStandardTemplate();

		$xdglRequest = xdglRequest::getInstanceForDigiLitObjectId($this->obj_id);

		if ($xdglRequest->getId()) {
			self::initHeader($xdglRequest->getTitle());
		} else {

			self::initHeader($this->pl->txt('obj_xdgl_title'));
		}

		switch ($next_class) {
			case 'ilpermissiongui':
				$this->setTabs();
				$this->tabs_gui->setTabActive('permissions');
				$perm_gui = new ilPermissionGUI($this);
				$this->ctrl->forwardCommand($perm_gui);
				break;
			case 'ilinfoscreengui':
				$this->setTabs();
				$this->tabs_gui->setTabActive('info');
				$info_gui = new ilInfoScreenGUI($this);
				$this->ctrl->forwardCommand($info_gui);
				break;
			case 'srobjDigiLitgui':
			case '':
				$this->setTabs();
				switch ($cmd) {
					case 'create':
						$this->tabs_gui->clearTargets();
						$this->create();
						break;
					case 'save':
						$this->save();
						break;
					case self::CMD_REDIRECT_PARENT_GUI:
						$this->redirectParentGui();
						break;
					case 'edit':
						// case 'update':
						$this->edit();
						break;
					case 'update':
						parent::update();
						break;
					case 'sendFile':
						$this->$cmd();
						break;

					case self::CMD_SHOW_CONTENT:
					case '':
					case 'cancel':
						$this->ctrl->returnToParent($this);
						break;
					case 'infoScreen':
						$this->ctrl->setCmd('showSummary');
						$this->ctrl->setCmdClass('ilinfoscreengui');
						$this->infoScreen();
						$this->tpl->show();
						break;
					case self::CMD_CONFIRM_DELETE_OBJECT:
						$this->$cmd();
						break;
					case self::CMD_DELETE_DIGI_LIT:
						$this->confirmedDelete();
						break;
				}
				break;
		}
	}


	/**
	 * @return string
	 */
	function getAfterCreationCmd() {
		return self::CMD_REDIRECT_PARENT_GUI;
	}


	public function redirectParentGui() {
		ilUtil::redirect(ilLink::_getLink($this->getParentRefId()));
	}

	/**
	 * @return int
	 */
	public function getParentRefId($ref_id = NULL) {
		global $tree;
		/**
		 * @var $tree ilTree
		 */
		if (! $ref_id) {
			$ref_id = $_GET['ref_id'];
		}

		return $tree->getParentId($ref_id);
	}


	/**
	 * @return string
	 */
	function getStandardCmd() {
		return self::CMD_SHOW_CONTENT;
	}


	protected function setTabs() {
		return true;
	}


	/**
	 * Init creation froms
	 *
	 * this will create the default creation forms: new, import, clone
	 *
	 * @param    string $a_new_type
	 *
	 * @return    array
	 */
	protected function initCreationForms($a_new_type) {
		$this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::XDGL);

		return array( self::CFORM_NEW => $this->initCreateForm($a_new_type) );
	}


	/**
	 * @param string $type
	 *
	 * @return ilPropertyFormGUI
	 */
	public function initCreateForm($type) {
		$creation_form = new xdglRequestFormGUI($this, new xdglRequest());
		$creation_form->fillForm(ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']));
		global $ilUser;
		/**
		 * @var $ilUser ilObjUser
		 */
		if ((strpos(gethostname(), '.local') OR strpos(gethostname(), 'vagrant-') === 0) AND $ilUser->getId() == 6) {
			$creation_form->fillFormRandomized();
		}

		return $creation_form->getAsPropertyFormGui();
	}


	public function save() {
		$creation_form = new xdglRequestFormGUI($this, new xdglRequest());
		$creation_form->setValuesByPost();
		if ($request_id = $creation_form->saveObject(ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']))) {
			$this->saveObject($request_id);
		} else {
			$creation_form->setValuesByPost();
			$this->tpl->setContent($creation_form->getHtml());
		}
	}


	/**
	 * @description provide file as a download
	 */
	public function sendFile() {
		global $lng;
		if (ilObjDigiLitAccess::hasAccessToDownload($this->ref_id)) {
			/**
			 * @var $xdglRequest xdglRequest
			 */
			$xdglRequest = xdglRequest::find($_GET['xdgl_id']);
			if (! $xdglRequest->deliverFile()) {
				ilUtil::sendFailure($lng->txt('file_not_found'));
			}
		} else {
			ilUtil::sendFailure($this->lng->txt('no_permission'), true);
			ilObjectGUI::_gotoRepositoryRoot();
		}
	}


	/**
	 * @param $title
	 */
	public static function initHeader($title) {
		global $tpl;
		$pl = ilDigiLitPlugin::getInstance();
		$tpl->setTitle($title);
		$tpl->setDescription('');
		if (xdglConfig::is50()) {
			$tpl->setTitleIcon(ilUtil::getImagePath('icon_xdgl.svg', 'Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit'));
		} else {
			$tpl->setTitleIcon($pl->getImagePath('icon_' . ilDigiLitPlugin::getStaticPluginPrefix() . '_b.png'), $pl->txt('xdgl_icon') . ' '
				. $pl->txt('obj_' . ilDigiLitPlugin::getStaticPluginPrefix()));
		}
	}


	public function infoScreen() {
		$info = new ilInfoScreenGUI($this);
		$info->addSection($this->txt('request_metadata'));
		$xdglRequest = xdglRequest::getInstanceForDigiLitObjectId($this->obj_id);
		$xdglRequestFormGUI = new xdglRequestFormGUI($this, $xdglRequest, true, true);
		$xdglRequestFormGUI->fillForm();
		/**
		 * @var $item ilTextInputGUI
		 */
		foreach ($xdglRequestFormGUI->getItems() as $item) {
			$info->addProperty($item->getTitle(), $item->getValue());
		}

		$info->enablePrivateNotes();
		$this->addInfoItems($info);
		$this->ctrl->forwardCommand($info);
	}


	public function confirmDeleteObject() {
		$a_val = array( $_GET['ref_id'] );
		ilSession::set('saved_post', $a_val);
		$ru = new ilRepUtilGUI($this);
		if (! $ru->showDeleteConfirmation($a_val, false)) {
			$this->redirectParentGui();
		}
		$this->tpl->show();
	}


	public function confirmedDelete() {
		if (isset($_POST['mref_id'])) {
			$_SESSION['saved_post'] = array_unique(array_merge($_SESSION['saved_post'], $_POST['mref_id']));
		}
		$ref_id = $_SESSION['saved_post'][0];
		$parent_ref_id = $this->getParentRefId($ref_id);
		$xdglRequest = xdglRequest::getInstanceForDigiLitObjectId(ilObject2::_lookupObjId($ref_id));
		$xdglRequest->setStatus(xdglRequest::STATUS_DELETED);
		$xdglRequest->update();
		$ru = new ilRepUtilGUI($this);
		$ru->deleteObjects(ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']), ilSession::get('saved_post'));
		ilSession::clear('saved_post');
		ilUtil::redirect(ilLink::_getLink($parent_ref_id));
	}
}

?>