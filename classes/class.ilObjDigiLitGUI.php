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

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/vendor/autoload.php');

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
 * @ilCtrl_Calls      ilObjDigiLitGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, xdglSearchGUI
 *
 */
class ilObjDigiLitGUI extends ilObjectPluginGUI {

	const CMD_CONFIRM_DELETE_OBJECT = 'confirmDeleteObject';
	const CMD_REDIRECT_PARENT_GUI = 'redirectParentGui';
	const CMD_SHOW_CONTENT = 'showContent';
	const CMD_SEND_FILE = 'sendFile';
	const CMD_DELETE_DIGI_LIT = 'confirmedDelete';
	const CMD_CREATE = 'create';
	const CMD_SAVE = 'save';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_CANCEL = 'cancel';
	const CMD_INFO_SCREEN = 'infoScreen';
	/**
	 * @var ilObjDigiLit
	 */
	public $object;
	/**
	 * @var \xdglRequest
	 */
	protected $xdglRequest;
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
	 * @param \ilObject $newObj
	 * @param bool      $only_parent_func
	 */
	public function afterSave(ilObject $newObj) {
		$numargs = func_get_args();
		if(is_array($numargs[1])) {
			$request = new xdglRequest($numargs[1][0]);
		} else {
			$request = new xdglRequest($numargs[1]);
		}
		$request->setDigiLitObjectId($newObj->getId());
		$request->update();
		parent::afterSave($newObj);
	}


	/**
	 * @return string
	 */
	final public function getType() {
		return ilDigiLitPlugin::XDGL;
	}


	public function executeCommand() {
		if ($this->access->checkAccess('read', '', $_GET['ref_id'])) {
			$this->history->addItem($_GET['ref_id'], $this->ctrl->getLinkTarget($this, self::CMD_SEND_FILE), $this->getType(), '');
		}
		$cmd = $this->ctrl->getCmd();
		$next_class = $this->ctrl->getNextClass($this);
		$this->tpl->getStandardTemplate();

		$this->xdglRequest = xdglRequest::find($_GET['xdgl_id']);

		if ($this->xdglRequest->getId()) {
			self::initHeader($this->xdglRequest->getTitle());
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
			case 'xdglsearchgui':
				$search_gui = new xdglSearchGUI();
				$this->ctrl->forwardCommand($search_gui);
				break;
			case 'srobjDigiLitgui':
			case '':
				$this->setTabs();
				switch ($cmd) {
					case self::CMD_CREATE:
						$this->tabs_gui->clearTargets();
						if(xdglConfig::getConfigValue(xdglConfig::F_USE_SEARCH)) {
							//TODO change method to search
							$this->ctrl->redirectByClass([self::class, xdglSearchGUI::class], xdglSearchGUI::CMD_STANDARD);
						} else {
							$this->create();
						}
						break;
					case self::CMD_SAVE:
						$this->save();
						break;
					case self::CMD_REDIRECT_PARENT_GUI:
						$this->redirectParentGui();
						break;
					case self::CMD_EDIT:
						// case 'update':
						$this->edit();
						break;
					case self::CMD_UPDATE:
						parent::update();
						break;
					case self::CMD_SEND_FILE:
						$this->$cmd();
						break;
					case '':
					case self::CMD_SHOW_CONTENT:
						$this->sendFile();
						break;
					case self::CMD_CANCEL:
						$this->ctrl->returnToParent($this);
						break;
					case self::CMD_INFO_SCREEN:
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
	public function getParentRefId($ref_id = null) {
		global $tree;
		/**
		 * @var $tree ilTree
		 */
		if (!$ref_id) {
			$ref_id = $_GET['ref_id'];
		}

		return $tree->getParentId($ref_id);
	}


	/**
	 * @return string
	 */
	function getStandardCmd() {
		return self::CMD_SEND_FILE;
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

		return array(self::CFORM_NEW => $this->initCreateForm($a_new_type));
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
	protected function sendFile() {
		global $lng;
		if (ilObjDigiLitAccess::hasAccessToDownload($this->ref_id)) {
			if (!$this->xdglRequest) {
				$this->xdglRequest = xdglRequest::find($_GET['xdgl_id']);
			}
			if (!$this->xdglRequest->deliverFile()) {
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
			$tpl->setTitleIcon($pl->getImagePath('icon_' . ilDigiLitPlugin::getStaticPluginPrefix() . '_b.png'),
				$pl->txt('xdgl_icon') . ' ' . $pl->txt('obj_' . ilDigiLitPlugin::getStaticPluginPrefix()));
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
		$a_val = array($_GET['ref_id']);
		ilSession::set('saved_post', $a_val);
		$ru = new ilRepUtilGUI($this);
		if (!$ru->showDeleteConfirmation($a_val, false)) {
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


	public static function _goto($a_target) {
		global $ilCtrl;
		$obj_id = ilObject2::_lookupObjId($a_target[0]);
		$a_value = xdglRequest::getIdByDigiLitObjectId($obj_id);
		$ilCtrl->initBaseClass(ilObjPluginDispatchGUI::class);
		$ilCtrl->setTargetScript('ilias.php');
		$ilCtrl->setParameterByClass(ilObjDigiLitGUI::class, xdglRequestGUI::XDGL_ID, $a_value);
		$ilCtrl->setParameterByClass(ilObjDigiLitGUI::class, 'ref_id', $a_target[0]);
		$ilCtrl->redirectByClass([ilObjPluginDispatchGUI::class, ilObjDigiLitGUI::class], ilObjDigiLitGUI::CMD_SEND_FILE);
	}

	public function putObjectInTree(ilObject $a_obj, $a_parent_node_id = NULL) {
		parent::putObjectInTree($a_obj, $a_parent_node_id); // TODO: Change the autogenerated stub
	}
}

