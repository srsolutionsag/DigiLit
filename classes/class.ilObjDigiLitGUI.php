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

use srag\DIC\DigiLit\DICTrait;

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
class ilObjDigiLitGUI extends ilObjectPluginGUI
{
    use DICTrait;

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
    const TAB_PERMISSIONS = 'permissions';
    const TAB_INFO = 'info';
    /**
     * @var ilObjDigiLit
     */
    public $object;
    /**
     * @var xdglRequest
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
    /**
     * @var ilLocator
     */
    protected $locator;
    /**
     * @var ilObjDigiLitFacadeFactory
     */
    protected $ilObjDigiLitFacadeFactory;

    protected function afterConstructor()
    {
        global $tpl, $ilCtrl, $ilAccess, $ilNavigationHistory, $ilTabs, $ilLocator;
        /**
         * @var ilTemplate          $tpl
         * @var ilCtrl              $ilCtrl
         * @var ilAccessHandler     $ilAccess
         * @var ilNavigationHistory $ilNavigationHistory
         */
        $this->tpl = $tpl;
        $this->history = $ilNavigationHistory;
        $this->access = $ilAccess;
        $this->ctrl = $ilCtrl;
        $this->tabs_gui = $ilTabs;
        $this->pl = ilDigiLitPlugin::getInstance();
        $this->locator = $ilLocator;
        $this->ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
    }

    /**
     * @param ilObject $newObj
     * @param bool     $only_parent_func
     */
    public function afterSave(ilObject $newObj)
    {
        global $DIC;
        $args = func_get_args();
        $xdglRequestUsage = new xdglRequestUsage();
        $xdglRequestUsage->setCrsRefId($DIC->repositoryTree()->getParentId($newObj->ref_id));
        $xdglRequestUsage->setRequestId($args[1][0]);
        $xdglRequestUsage->setObjId($newObj->getId());
        $xdglRequestUsage->create();
        parent::afterSave($newObj);
    }

    /**
     * @return string
     */
    final public function getType()
    {
        return ilDigiLitPlugin::PLUGIN_ID;
    }

    /**
     * @param int $ref_id
     */
    protected function isDigiLitObject($ref_id)
    {
        $obj_id = ilObject2::_lookupObjectId($ref_id);
        $ilObjDigiLit_rec = ilObjDigiLit::getObjectById($obj_id);
        if ($ilObjDigiLit_rec['type'] == self::getType()) {
            return true;
        }

        return false;
    }

    public function executeCommand()
    {

        $this->locator->addRepositoryItems();
        $this->tpl->setLocator();

        if (isset($_GET['xdgl_id'])) {
            $this->xdglRequest = xdglRequest::find($_GET['xdgl_id']);
        } else {
            $ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
            $request_usage = $ilObjDigiLitFacadeFactory->requestUsageFactory()->getInstanceByObjectId($this->obj_id);
            $this->xdglRequest = xdglRequest::find($request_usage->getRequestId());
        }

        if ($this->access->checkAccess('read', '', $_GET['ref_id']) && $this->isDigiLitObject($_GET['ref_id'])
            && ilObjDigiLitAccess::hasAccessToDownload($_GET['ref_id'])
            && $this->xdglRequest->getStatus() == xdglRequest::STATUS_RELEASED) {
            $this->history->addItem($_GET['ref_id'], $this->ctrl->getLinkTarget($this, self::CMD_SEND_FILE),
                $this->getType(), '');
        }
        $cmd = $this->ctrl->getCmd();
        $next_class = $this->ctrl->getNextClass($this);
        if (self::version()->is6()) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }

        if ($this->xdglRequest->getId()) {
            self::initHeader($this->xdglRequest->getTitle());
        } else {

            self::initHeader($this->pl->txt('obj_xdgl_title'));
        }

        switch ($next_class) {
            case 'ilpermissiongui':
                $this->setTabs();
                $this->tabs_gui->activateTab(self::TAB_PERMISSIONS);
                $perm_gui = new ilPermissionGUI($this);
                $this->ctrl->forwardCommand($perm_gui);
                break;
            case 'ilinfoscreengui':
                $this->setTabs();
                $this->tabs_gui->activateTab(self::TAB_INFO);
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
                        if (xdglConfig::getConfigValue(xdglConfig::F_USE_SEARCH)) {
                            //TODO: change method to search
                            $this->ctrl->redirectByClass([self::class, xdglSearchGUI::class],
                                xdglSearchGUI::CMD_STANDARD);
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
                        if (self::version()->is6()) {
                            $this->tpl->printToStdout();
                        } else {
                            $this->tpl->show();
                        }
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
    function getAfterCreationCmd()
    {
        return self::CMD_REDIRECT_PARENT_GUI;
    }

    public function redirectParentGui()
    {
        ilUtil::redirect(ilLink::_getLink($this->getParentRefId()));
    }

    /**
     * @return int
     */
    public function getParentRefId($ref_id = null)
    {
        global $tree;
        /**
         * @var ilTree $tree
         */
        if (!$ref_id) {
            $ref_id = $_GET['ref_id'];
        }

        return $tree->getParentId($ref_id);
    }

    /**
     * @return string
     */
    function getStandardCmd()
    {
        return self::CMD_SEND_FILE;
    }

    protected function setTabs()
    {
        return true;
    }

    /**
     * Init creation froms
     *
     * this will create the default creation forms: new, import, clone
     *
     * @param string $a_new_type
     *
     * @return array
     */
    protected function initCreationForms($a_new_type)
    {
        $this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::PLUGIN_ID);

        return array(self::CFORM_NEW => $this->initCreateForm($a_new_type));
    }

    /**
     * @param string $type
     *
     * @return ilPropertyFormGUI
     */
    public function initCreateForm($type)
    {
        $creation_form = new xdglRequestFormGUI($this, new xdglRequest());
        $creation_form->fillForm(ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']));
        global $ilUser;
        /**
         * @var ilObjUser $ilUser
         */
        if ((strpos(gethostname(), '.local') or strpos(gethostname(), 'vagrant-') === 0) and $ilUser->getId() == 6) {
            $creation_form->fillFormRandomized();
        }

        return $creation_form->getAsPropertyFormGui();
    }

    public function save()
    {
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
    protected function sendFile()
    {
        global $lng;
        if (ilObjDigiLitAccess::hasAccessToDownload($this->ref_id)) {
            if (empty($this->xdglRequest)) {
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
     * @param string $title
     */
    public static function initHeader($title)
    {
        global $tpl;
        $pl = ilDigiLitPlugin::getInstance();
        $tpl->setTitle($title);
        $tpl->setDescription('');
        $tpl->setTitleIcon(ilUtil::getImagePath('icon_xdgl.svg',
            'Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit'));
    }

    public function infoScreen()
    {
        $info = new ilInfoScreenGUI($this);
        $info->addSection($this->txt('request_metadata'));
        $xdglRequestUsage = $this->ilObjDigiLitFacadeFactory->requestUsageFactory()->getInstanceByObjectId($this->obj_id);
        $xdglRequest = new xdglRequest($xdglRequestUsage->getRequestId());
        $xdglRequestFormGUI = new xdglRequestFormGUI($this, $xdglRequest, true, true);
        $xdglRequestFormGUI->fillForm();
        /**
         * @var ilTextInputGUI $item
         */
        foreach ($xdglRequestFormGUI->getItems() as $item) {
            $info->addProperty($item->getTitle(), $item->getValue());
        }

        $info->enablePrivateNotes();
        $this->addInfoItems($info);
        $this->ctrl->forwardCommand($info);
    }

    public function confirmDeleteObject()
    {
        $a_val = array($_GET['ref_id']);
        ilSession::set('saved_post', $a_val);
        $ru = new ilRepUtilGUI($this);
        if (!$ru->showDeleteConfirmation($a_val, false)) {
            $this->redirectParentGui();
        }
        if (self::version()->is6()) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }

    public function confirmedDelete()
    {
        if (isset($_POST['mref_id'])) {
            $_SESSION['saved_post'] = array_unique(array_merge($_SESSION['saved_post'], $_POST['mref_id']));
        }
        $ref_id = $_SESSION['saved_post'][0];
        $parent_ref_id = $this->getParentRefId($ref_id);
        $ru = new ilRepUtilGUI($this);
        $ru->deleteObjects(ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']), ilSession::get('saved_post'));
        ilSession::clear('saved_post');
        ilUtil::redirect(ilLink::_getLink($parent_ref_id));
    }

    public static function _goto($a_target)
    {
        global $ilCtrl;
        $ilObjDigiLitFacadeFacory = new ilObjDigiLitFacadeFactory();
        $obj_id = ilObject2::_lookupObjId($a_target[0]);
        $a_value = $ilObjDigiLitFacadeFacory->requestUsageFactory()->getInstanceByObjectId($obj_id)->getRequestId();
        $ilCtrl->initBaseClass(ilObjPluginDispatchGUI::class);
        $ilCtrl->setTargetScript('ilias.php');
        $ilCtrl->setParameterByClass(ilObjDigiLitGUI::class, xdglRequestGUI::XDGL_ID, $a_value);
        $ilCtrl->setParameterByClass(ilObjDigiLitGUI::class, 'ref_id', $a_target[0]);
        $ilCtrl->redirectByClass([ilObjPluginDispatchGUI::class, ilObjDigiLitGUI::class],
            ilObjDigiLitGUI::CMD_INFO_SCREEN);
    }

    public function putObjectInTree(ilObject $a_obj, $a_parent_node_id = null)
    {
        parent::putObjectInTree($a_obj, $a_parent_node_id); // TODO: Change the autogenerated stub
    }
}
