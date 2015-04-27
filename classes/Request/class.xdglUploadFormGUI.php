<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/class.ilDigiLitPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/class.ilObjDigiLit.php');

/**
 * GUI-Class xdglRequestFormGUI
 *
 * @author            Martin Studer <ms@studer-raimann.ch>
 * @author            Gabriel Comte <gc@studer-raimann.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.00
 *
 */
class xdglUploadFormGUI extends ilPropertyFormGUI {

	const F_FILE_UPLOAD = 'file_upload';
	/**
	 * @var  xdglRequest
	 */
	protected $request;
	/**
	 * @var ilObjDigiLitGUI
	 */
	protected $parent_gui;
	/**
	 * @var  ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	/**
	 * @var String
	 */
	protected $upload_name;
	/**
	 * @var String
	 */
	protected $upload_temp_name;


	/**
	 * @param             $parent_gui
	 * @param xdglRequest $request
	 */
	public function __construct($parent_gui, xdglRequest $request) {
		global $ilCtrl;
		$this->request = $request;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->replace = $this->request->fileExists();
		var_dump($this->replace); // FSX
		$this->pl = ilDigiLitPlugin::getInstance();
		if ($_GET['rl'] == 'true') {
			$this->pl->updateLanguageFiles();
		}
		$this->ctrl->saveParameter($parent_gui, xdglRequestGUI::XDGL_ID);
		$this->initForm();
	}


	protected function initForm() {
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		if ($this->replace) {
			$this->setTitle($this->pl->txt('replace_title'));
		} else {
			$this->setTitle($this->pl->txt('upload_title'));
		}

		// Add File-Upload
		$in_file = new ilFileInputGUI($this->pl->txt('upload_file'), self::F_FILE_UPLOAD);
		$in_file->setSuffixes(array( 'pdf' ));
		$in_file->setRequired(true);
		$this->addItem($in_file);

		if ($this->replace) {
			$this->addCommandButton(xdglRequestGUI::CMD_UPLOAD, $this->pl->txt('upload_replace'));
		} else {
			$this->addCommandButton(xdglRequestGUI::CMD_UPLOAD, $this->pl->txt('upload_save'));
		}

		$this->addCommandButton(xdglRequestGUI::CMD_CANCEL, $this->pl->txt('upload_cancel'));
	}


	protected function readForm() {
		if (!$this->checkInput()) {
			return false;
		}
		$this->upload_name = $_FILES[self::F_FILE_UPLOAD]['name'];
		$this->upload_temp_name = $_FILES[self::F_FILE_UPLOAD]['tmp_name'];
	}


	/**
	 * @return bool
	 * @throws Exception
	 */
	public function uploadFile() {
		$this->readForm();
		if ($this->request->uploadFileFromForm($this)) {
			global $ilUser;
			$this->request->setLibrarianId($ilUser->getId());
			$this->request->update();
			ilUtil::sendSuccess($this->pl->txt('msg_success_upload'), true);
			xdglNotification::sendUploaded($this->request);

			return true;
		}

		return false;
	}


	/**
	 * @return String
	 */
	public function getUploadName() {
		return $this->upload_name;
	}


	/**
	 * @param String $upload_name
	 */
	public function setUploadName($upload_name) {
		$this->upload_name = $upload_name;
	}


	/**
	 * @return String
	 */
	public function getUploadTempName() {
		return $this->upload_temp_name;
	}


	/**
	 * @param String $upload_temp_name
	 */
	public function setUploadTempName($upload_temp_name) {
		$this->upload_temp_name = $upload_temp_name;
	}
}