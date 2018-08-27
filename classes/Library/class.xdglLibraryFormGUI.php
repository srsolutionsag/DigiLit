<?php

/**
 * Class xdglLibraryFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglLibraryFormGUI extends ilPropertyFormGUI {

	const F_TITLE = 'title';
	const F_DESCRIPTION = 'description';
	const F_EMAIL = 'email';
	const F_LIBRARIAN_COUNT = 'librarian_count';
	const F_REQUEST_COUNT = 'request_count';
	const F_ACTIVE = 'active';
	/**
	 * @var xdglLibrary
	 */
	protected $library;
	/**
	 * @var xdglLibraryGUI
	 */
	protected $parent_gui;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;


	/**
	 * @param xdglLibraryGUI $parent_gui
	 * @param xdglLibrary    $library
	 * @param bool           $view
	 */
	public function __construct($parent_gui, xdglLibrary $library, $view = false) {
		parent::__construct();
		global $ilCtrl;
		$this->library = $library;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->ctrl->saveParameter($parent_gui, xdglLibraryGUI::XDGL_LIB_ID);
		$this->is_new = ($this->library->getId() == 0);
		$this->view = $view;
		if ($view) {
			$this->initView();
		} else {
			$this->initForm();
		}
	}


	protected function initView() {
		$this->initForm();
		/**
		 * @var ilNonEditableValueGUI $item
		 */
		foreach ($this->getItems() as $item) {
			$te = new ilNonEditableValueGUI($this->txt($item->getPostVar()), $item->getPostVar());
			$this->removeItemByPostVar($item->getPostVar());
			$this->addItem($te);
		}
		$te = new ilNonEditableValueGUI($this->txt(self::F_REQUEST_COUNT), self::F_REQUEST_COUNT);
		$this->addItem($te);

		$te = new ilNonEditableValueGUI($this->txt(self::F_LIBRARIAN_COUNT), self::F_LIBRARIAN_COUNT);
		$this->addItem($te);
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('library_' . $key);
	}


	protected function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$this->setTitle($this->txt('form_title'));

		$te = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilTextAreaInputGUI($this->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
		$this->addItem($te);

		$te = new ilTextInputGUI($this->txt(self::F_EMAIL), self::F_EMAIL);
		$te->setRequired(true);
		$this->addItem($te);

		$cb = new ilCheckboxInputGUI($this->txt(self::F_ACTIVE), self::F_ACTIVE);
		$this->addItem($cb);
	}


	public function fillForm() {
		$array = array(
			self::F_TITLE => $this->library->getTitle(),
			self::F_DESCRIPTION => $this->library->getDescription(),
			self::F_EMAIL => $this->library->getEmail(),
			self::F_REQUEST_COUNT => $this->library->getRequestCount(),
			self::F_LIBRARIAN_COUNT => $this->library->getLibrarianCount(),
			self::F_ACTIVE => $this->library->isActive(),
		);

		$this->setValuesByArray($array);
	}


	/**
	 * returns whether checkinput was successful or not.
	 *
	 * @return bool
	 */
	public function fillObject() {
		if (!$this->checkInput()) {
			return false;
		}
		$this->library->setTitle($this->getInput(self::F_TITLE));
		$this->library->setDescription($this->getInput(self::F_DESCRIPTION));
		$this->library->setEmail($this->getInput(self::F_EMAIL));
		$this->library->setActive($this->getInput(self::F_ACTIVE));

		return true;
	}


	/**
	 * @return bool false when unsuccessful or int request_id when successful
	 */
	public function saveObject() {
		if (!$this->fillObject()) {
			return false;
		}
		if ($this->library->getId() > 0) {
			$this->library->update();
		} else {
			$this->library->create();
		}

		return $this->library->getId();
	}


	protected function initButtons() {
		if (!$this->view) {
			if ($this->library->getId() == 0) {
				$this->addCommandButton(xdglLibraryGUI::CMD_CREATE, $this->txt('create'));
			} else {
				$this->addCommandButton(xdglLibraryGUI::CMD_UPDATE, $this->txt('update'));
			}
		} else {
			$this->addCommandButton(xdglLibraryGUI::CMD_EDIT, $this->txt('edit'));
		}

		$this->addCommandButton(xdglLibraryGUI::CMD_CANCEL, $this->txt('cancel'));
	}
}


