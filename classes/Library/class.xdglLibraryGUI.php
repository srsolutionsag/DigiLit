<?php

/**
 * Class xdglLibraryGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_isCalledBy xdglLibraryGUI : xdglMainGUI, ilObjComponentSettingsGUI
 */
class xdglLibraryGUI {

	const XDGL_LIB_ID = 'lib_id';
	const CMD_STANDARD = 'index';
	const CMD_EDIT = 'edit';
	const CMD_VIEW = 'view';
	const CMD_CONFIRM_DELETE = 'confirmDelete';
	const CMD_CREATE = 'create';
	const CMD_UPDATE = 'update';
	const CMD_DELETE = 'delete';
	const CMD_CANCEL = 'cancel';
	const CMD_RETURN_TO_REQUESTS = 'returnToRequests';
	const CMD_ASSIGN_LIBRARY = 'assignLibrary';
	const CMD_ADD = 'add';
	const CMD_PERFORM_LIBRARY_ASSIGNEMENT = 'performLibraryAssignment';
	const CMD_GET_AJAX_DATA = 'getAjaxData';
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var xdglLibrary
	 */
	protected $library;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs_gui;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs;
		$this->tpl = $tpl;
		$this->tabs_gui = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->library = xdglLibrary::find($_GET[self::XDGL_LIB_ID]);
	}


	/**
	 * @return bool
	 */
	public function executeCommand() {
		$this->ctrl->saveParameter($this, self::XDGL_LIB_ID);
		$this->ctrl->saveParameter($this, xdglRequestGUI::XDGL_ID);

		switch ($this->ctrl->getNextClass()) {
			case 'xdgllibrariangui':
				$xdglLibrarianGUI = new xdglLibrarianGUI();
				$this->ctrl->forwardCommand($xdglLibrarianGUI);

				return;
		}

		$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);

		switch ($cmd) {
			case self::CMD_STANDARD:
			case self::CMD_EDIT:
			case self::CMD_UPDATE:
			case self::CMD_CONFIRM_DELETE:
			case self::CMD_CREATE:
			case self::CMD_ADD:
			case self::CMD_CANCEL:
			case self::CMD_VIEW:
			case self::CMD_DELETE:
				if (!ilObjDigiLitAccess::isAdmin()) {
					return false;
				}
				$this->{$cmd}();
				break;
			case self::CMD_ASSIGN_LIBRARY:
			case self::CMD_RETURN_TO_REQUESTS:
			case self::CMD_GET_AJAX_DATA:
			case self::CMD_PERFORM_LIBRARY_ASSIGNEMENT:
				$this->tabs_gui->clearTargets();
				$this->tabs_gui->setBackTarget($this->pl->txt('library_back'), $this->ctrl->getLinkTargetByClass('xdglRequestGUI'));
				$this->{$cmd}();
				break;
		}

		return true;
	}


	protected function index() {
		$xdglLibraryTableGUI = new xdglLibraryTableGUI($this, self::CMD_STANDARD);
		$this->tpl->setContent($xdglLibraryTableGUI->getHTML());
	}


	protected function edit() {
		$xdglLibraryFormGUI = new xdglLibraryFormGUI($this, $this->library);
		$xdglLibraryFormGUI->fillForm();
		$this->tpl->setContent($xdglLibraryFormGUI->getHTML());
	}


	protected function update() {
		$xdglLibraryFormGUI = new xdglLibraryFormGUI($this, $this->library);
		$xdglLibraryFormGUI->setValuesByPost();
		if ($xdglLibraryFormGUI->saveObject()) {
			ilUtil::sendSuccess($this->pl->txt('msg_success_edit'), true);
			$this->ctrl->redirect($this, self::CMD_CANCEL);
		}
		$this->tpl->setContent($xdglLibraryFormGUI->getHTML());
	}


	protected function add() {
		$xdglLibraryFormGUI = new xdglLibraryFormGUI($this, new xdglLibrary());
		$xdglLibraryFormGUI->fillForm();
		$this->tpl->setContent($xdglLibraryFormGUI->getHTML());
	}


	protected function view() {
		$xdglLibraryFormGUI = new xdglLibraryFormGUI($this, $this->library, true);
		$xdglLibraryFormGUI->fillForm();
		$this->tpl->setContent($xdglLibraryFormGUI->getHTML());
	}


	protected function create() {
		$xdglLibraryFormGUI = new xdglLibraryFormGUI($this, new xdglLibrary());
		$xdglLibraryFormGUI->setValuesByPost();
		if ($xdglLibraryFormGUI->saveObject()) {
			ilUtil::sendSuccess($this->pl->txt('msg_success_add'), true);
			$this->ctrl->redirect($this, self::CMD_CANCEL);
		}
		$this->tpl->setContent($xdglLibraryFormGUI->getHTML());
	}


	protected function confirmDelete() {
		if (!$this->library->isDeletable()) {
			throw new ilException('This Library can not be deleted');
		}
		$conf = new ilConfirmationGUI();
		$conf->setFormAction($this->ctrl->getFormAction($this));
		$conf->setHeaderText($this->pl->txt('msg_confirm_delete_library'));
		$conf->addItem(self::XDGL_LIB_ID, $this->library->getId(), $this->library->getTitle());
		$conf->setConfirm($this->pl->txt('library_delete'), self::CMD_DELETE);
		$conf->setCancel($this->pl->txt('library_cancel'), self::CMD_CANCEL);
		$this->tpl->setContent($conf->getHTML());
	}


	protected function delete() {
		if (!$this->library->isDeletable()) {
			throw new ilException('This Library can not be deleted');
		}
		$this->library->delete();
		$this->cancel();
	}


	protected function assignLibrary() {
		$ajax = new ilTemplate($this->pl->getDirectory() . '/templates/librarian.js', false, false);
		$ajax->setVariable('URL', $this->ctrl->getLinkTarget($this, self::CMD_GET_AJAX_DATA));
		$this->tpl->addOnLoadCode($ajax->get());

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->pl->txt('library_assign'));

		$se = new ilSelectInputGUI($this->pl->txt('library_select'), 'library_select');
		$se->setRequired(true);
		$se->setOptions(xdglLibrary::where(array('active' => true))->orderBy('title')->getArray('id', 'title'));
		$form->addItem($se);

		$se = new ilSelectInputGUI($this->pl->txt('librarian_select'), 'librarian_select');
		$se->setOptions(array_merge(array(0 => $this->pl->txt('librarian_none')), xdglLibrarian::getAssignedLibrariansForLibrary()));
		$form->addItem($se);

		$form->addCommandButton(self::CMD_RETURN_TO_REQUESTS, $this->pl->txt('library_cancel'));
		$form->addCommandButton(self::CMD_PERFORM_LIBRARY_ASSIGNEMENT, $this->pl->txt('library_perform_assignment'));

		$this->tpl->setContent($form->getHTML());
	}


	protected function performLibraryAssignment() {
		/**
		 * $request xdglRequest
		 */
		$request = xdglRequest::find($_GET[xdglRequestGUI::XDGL_ID]);

		$xdglLibrarian = xdglLibrarian::find($_POST['librarian_select'], $_POST['library_select']);
		$xdglLibrary = xdglLibrary::find($_POST['library_select']);

		if ($xdglLibrarian instanceof xdglLibrarian) {
			if ($xdglLibrarian->getUsrId()) {
				$request->assignToLibrarian($xdglLibrarian);
			} elseif ($xdglLibrary instanceof xdglLibrary) {
				$request->assignToLibrary($xdglLibrary);
			}
		} elseif ($xdglLibrary instanceof xdglLibrary) {
			$request->assignToLibrary($xdglLibrary);
		}
		$this->returnToRequests();
	}


	protected function cancel() {
		$this->ctrl->setParameter($this, self::XDGL_LIB_ID, null);
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function returnToRequests() {
		$this->ctrl->setParameterByClass('xdglRequestGUI', xdglRequestGUI::XDGL_ID, null);
		$this->ctrl->redirectByClass('xdglRequestGUI', xdglRequestGUI::CMD_INDEX);
	}


	protected function getAjaxData() {
		$form = '<option value="0" >' . $this->pl->txt('librarian_none') . '</option>';
		foreach (xdglLibrarian::getAssignedLibrariansForLibrary($_GET['lib_id']) as $usr_id => $name) {
			$form .= '<option value="' . $usr_id . '" >' . $name . '</option>';
		}
		echo $form;
		exit;
	}
}


