<?php

/**
 * GUI-Class xdglRequestGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Martin Studer <ms@studer-raimann.ch>
 * @version           1.0.00
 *
 * @ilCtrl_isCalledBy xdglRequestGUI : xdglMainGUI, ilObjComponentSettingsGUI
 */
class xdglRequestGUI {

	const XDGL_ID = 'xdgl_id';
	const CDM_CONFIRM_REFUSE = 'confirmRefuse';
	const CMD_SELECT_FILE = 'selectFile';
	const CMD_CANCEL = 'cancel';
	const CMD_INDEX = 'index';
	const CMD_UPLOAD = 'upload';
	const CMD_EDIT = 'edit';
	const CMD_REPLACE_FILE = 'replaceFile';
	const CMD_DOWNLOAD_FILE = 'downloadFile';
	const CMD_DELETE_FILE = 'confirmDeleteFile';
	const CMD_PERFORM_DELETE_FILE = 'deleteFile';
	const CMD_DELETE_REQUEST = 'confirmDeleteRequest';
	const CMD_PERFORM_DELETE_REQUEST = 'deleteRequest';
	const CMD_SAVE = 'save';
	const CMD_VIEW = 'view';
	const CMD_UPDATE = 'update';
	const CMD_CONFIRM_REFUSE = 'confirmRefuse';
	const CMD_CHANGE_STATUS_TO_REFUSED = 'changeStatusToRefused';
	const CMD_CHANGE_STATUS_TO_WIP = 'changeStatusToWip';
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_SANDBOX = 'sandbox';
	const F_REASON = 'reason';
	/**
	 * @var ilTabsGUI
	 */
	protected $ilTabs;
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
	/**
	 * @var  srObjAlbum
	 */
	protected $album;
	/**
	 * @var ilObjPhotoGallery
	 */
	public $obj_photo_gallery;
	/**
	 * @var ilAccessHandler
	 */
	protected $access;
	/**
	 * @var ilObjPhotoGalleryGUI
	 */
	protected $parent_gui;
	/**
	 * @var xdglRequest
	 */
	protected $xdglRequest;
	/**
	 * @var ilObjDigiLitFacadeFactory
	 */
	protected $ilObjDigiLitFacadeFactory;


	public function __construct() {
		global $tpl, $ilCtrl, $ilToolbar, $ilAccess, $ilTabs;
		$this->ilTabs = $ilTabs;
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->tpl = $tpl;
		$this->access = $ilAccess;
		$this->ctrl = $ilCtrl;
		$this->toolbar = $ilToolbar;
		$this->ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();

		if ($_GET['rl'] == 'true') {
			$this->pl->updateLanguageFiles();
		}

		$this->xdglRequest = xdglRequest::find($_GET[self::XDGL_ID]);
		$this->ctrl->saveParameter($this, self::XDGL_ID);
		ilObjDigiLitGUI::initHeader($this->pl->txt('obj_xdgl_admin_title'));
	}


	/**
	 * @return bool
	 */
	public function executeCommand() {
		ilObjDigiLitAccess::isManager(true);
		$cmd = $this->ctrl->getCmd(self::CMD_INDEX);
		switch ($cmd) {
			case self::CMD_INDEX:
			case self::CMD_CANCEL:
			case 'configure':
			case '':
				$this->index();
				break;
			case self::CDM_CONFIRM_REFUSE:
			case self::CMD_UPLOAD:
			case self::CMD_EDIT:
			case self::CMD_SAVE:
			case self::CMD_VIEW:
			case self::CMD_UPDATE:
			case self::CMD_CONFIRM_REFUSE:
			case self::CMD_CHANGE_STATUS_TO_REFUSED:
			case self::CMD_CHANGE_STATUS_TO_WIP:
			case self::CMD_APPLY_FILTER:
			case self::CMD_RESET_FILTER:
			case self::CMD_SELECT_FILE:
			case self::CMD_REPLACE_FILE:
			case self::CMD_DELETE_FILE:
			case self::CMD_PERFORM_DELETE_FILE:
			case self::CMD_DELETE_REQUEST:
			case self::CMD_PERFORM_DELETE_REQUEST:
			case self::CMD_SANDBOX:
			case self::CMD_DOWNLOAD_FILE:
				$this->$cmd();
				break;
		}

		return true;
	}


	public function index() {
		ilObjDigiLitAccess::isManager(true);
		$xdglRequestTableGUI = new xdglRequestTableGUI($this, $this->ctrl->getCmd());
		$this->tpl->setContent($xdglRequestTableGUI->getHTML());
	}


	protected function applyFilter() {
		ilObjDigiLitAccess::isManager(true);
		$tableGui = new xdglRequestTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset(true);
		$tableGui->writeFilterToSession();
		$this->ctrl->redirect($this, self::CMD_INDEX);
	}


	protected function resetFilter() {
		ilObjDigiLitAccess::isManager(true);
		$tableGui = new xdglRequestTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset();
		$tableGui->resetFilter();
		$this->ctrl->redirect($this, self::CMD_INDEX);
	}


	public function view() {
		ilObjDigiLitAccess::isManager(true);
		$xdglRequestFormGUI = new xdglRequestFormGUI($this, $this->xdglRequest, true, false, false);
		$xdglRequestFormGUI->fillForm();
		$this->tpl->setContent($xdglRequestFormGUI->getHTML());
	}


	public function edit() {
		ilObjDigiLitAccess::isManager(true);
		$xdglRequestFormGUI = new xdglRequestFormGUI($this, $this->xdglRequest, false, false, false);
		$xdglRequestFormGUI->fillForm();
		$this->tpl->setContent($xdglRequestFormGUI->getHTML());
	}


	public function update() {
		ilObjDigiLitAccess::isManager(true);
		$xdglRequestFormGUI = new xdglRequestFormGUI($this, $this->xdglRequest, false, false, false);
		$xdglRequestFormGUI->setValuesByPost();
		if ($xdglRequestFormGUI->saveObject(null)) {
			ilUtil::sendSuccess($this->pl->txt('msg_success_edit'), true);
			$this->ctrl->setParameter($this, self::XDGL_ID, null);
			$this->ctrl->redirect($this);
		} else {
			$this->tpl->setContent($xdglRequestFormGUI->getHTML());
		}
	}


	protected function save() {
		ilObjDigiLitAccess::isManager(true);
		$xdglRequestFormGUI = new xdglRequestFormGUI($this, $this->xdglRequest);
		$xdglRequestFormGUI->setValuesByPost();
		if ($xdglRequestFormGUI->saveObject(null)) {
			ilUtil::sendSuccess($this->pl->txt('msg_success_add'), true);
			$this->ctrl->setParameter($this, self::XDGL_ID, null);
			$this->ctrl->redirect($this);
		} else {
			$this->tpl->setContent($xdglRequestFormGUI->getHTML());
		}
	}


	protected function confirmRefuse() {
		ilObjDigiLitAccess::isManager(true);
		$form = $this->initRefuseForm();
		$this->tpl->setContent($form->getHTML());
	}


	protected function changeStatusToRefused() {
		ilObjDigiLitAccess::isManager(true);
		$form = $this->initRefuseForm();
		if ($form->checkInput()) {
			global $ilUser;
			$this->xdglRequest->setRejectionReason($form->getInput(self::F_REASON));
			$this->xdglRequest->setStatus(xdglRequest::STATUS_REFUSED);
			$this->xdglRequest->setLibrarianId($ilUser->getId());
			$this->xdglRequest->update();
			xdglNotification::sendRejected($this->xdglRequest);
			$this->ctrl->setParameter($this, self::XDGL_ID, null);
			$this->ctrl->redirect($this);
		} else {
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHTML());
		}
	}


	protected function changeStatusToWip() {
		global $ilUser;
		ilObjDigiLitAccess::isManager(true);
		$this->xdglRequest->setStatus(xdglRequest::STATUS_IN_PROGRRESS);
		$this->xdglRequest->setLibrarianId($ilUser->getId());
		$this->xdglRequest->update();
		$this->ctrl->setParameter($this, self::XDGL_ID, null);
		$this->ctrl->redirect($this);
	}


	protected function selectFile() {
		ilObjDigiLitAccess::isManager(true);
		$upload_form = new xdglUploadFormGUI($this, $this->xdglRequest);
		$this->tpl->setContent($upload_form->getHTML());
	}


	protected function replaceFile() {
		ilObjDigiLitAccess::isManager(true);
		$upload_form = new xdglUploadFormGUI($this, $this->xdglRequest);
		$this->tpl->setContent($upload_form->getHTML());
	}


	protected function upload() {
		ilObjDigiLitAccess::isManager(true);
		$upload_form = new xdglUploadFormGUI($this, $this->xdglRequest);
		$upload_form->uploadFile();

		$this->ctrl->setParameter($this, self::XDGL_ID, null);
		$this->ctrl->redirect($this);
	}


	protected function confirmDeleteFile() {
		ilObjDigiLitAccess::isManager(true);
		$ilConfirmationGUI = new ilConfirmationGUI();
		$ilConfirmationGUI->setFormAction($this->ctrl->getFormAction($this));
		$ilConfirmationGUI->setHeaderText($this->pl->txt('msg_request_delete_file'));
		$ilConfirmationGUI->setCancel($this->pl->txt('request_cancel'), self::CMD_CANCEL);
		$ilConfirmationGUI->setConfirm($this->pl->txt('request_delete_file'), self::CMD_PERFORM_DELETE_FILE);
		$ilConfirmationGUI->addItem(self::XDGL_ID, $this->xdglRequest->getId(), $this->xdglRequest->getTitle());
		$this->tpl->setContent($ilConfirmationGUI->getHTML());
	}


	/**
	 * @throws Exception
	 */
	protected function deleteFile() {
		ilObjDigiLitAccess::isManager(true);
		try {
			$this->xdglRequest->deleteFile();
			global $ilUser;
			$this->xdglRequest->setLibrarianId($ilUser->getId());
			ilUtil::sendSuccess($this->pl->txt('msg_request_file_deleted'), true);
		} catch (Exception $e) {
			ilUtil::sendFailure($e->getMessage(), true);
		}
		$this->ctrl->redirect($this);
	}

	protected function confirmDeleteRequest() {
		ilObjDigiLitAccess::isManager(true);
		$ilConfirmationGUI = new ilConfirmationGUI();
		$ilConfirmationGUI->setFormAction($this->ctrl->getFormAction($this));
		$xdglRequestUsageArray = $this->ilObjDigiLitFacadeFactory->requestUsageFactory()->getRequestUsagesArrayByRequestId($this->xdglRequest->getId());
		$crs_titles_array = $this->ilObjDigiLitFacadeFactory->requestUsageFactory()->getAllCoursTitlesWithRequestUsages($xdglRequestUsageArray);
		$ilConfirmationGUI->setCancel($this->pl->txt('request_cancel'), self::CMD_CANCEL);
		if(!empty($crs_titles_array)) {
			$ilConfirmationGUI->setHeaderText($this->pl->txt('msg_request_delete_with_usages') . "<pre>".implode(PHP_EOL,$crs_titles_array)."</pre>");
		}
		else {
			$ilConfirmationGUI->setHeaderText($this->pl->txt('msg_request_delete'));
			$ilConfirmationGUI->setConfirm($this->pl->txt('request_delete'), self::CMD_PERFORM_DELETE_REQUEST);
		}
		$ilConfirmationGUI->addItem(self::XDGL_ID, $this->xdglRequest->getId(), $this->xdglRequest->getTitle());
		$this->tpl->setContent($ilConfirmationGUI->getHTML());
	}


	/**
	 * @throws Exception
	 */
	protected function deleteRequest() {
		ilObjDigiLitAccess::isManager(true);
		try {
			$this->ilObjDigiLitFacadeFactory->requestUsageFactory()->deleteAllUsagesAndDigiLitObjectsByRequestId($this->xdglRequest->getId());
			$this->xdglRequest->setStatus(xdglRequest::STATUS_DELETED);
			ilUtil::sendSuccess($this->pl->txt('msg_request_deleted'), true);
		} catch (Exception $e) {
			ilUtil::sendFailure($e->getMessage(), true);
		}
		$this->ctrl->redirect($this);
	}


	protected function downloadFile() {
		ilObjDigiLitAccess::isManager(true);
		$this->xdglRequest->deliverFile();
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initRefuseForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->pl->txt('request_refuse'));

		$te = new ilTextareaInputGUI($this->pl->txt('request_refuse_reason'), self::F_REASON);
		$te->setCols(100);
		$te->setRequired(true);
		$te->setRows(10);
		$form->addItem($te);

		$form->addCommandButton(self::CMD_CHANGE_STATUS_TO_REFUSED, $this->pl->txt('request_refuse'));
		$form->addCommandButton(self::CMD_CANCEL, $this->pl->txt('request_cancel'));

		return $form;
	}
}

