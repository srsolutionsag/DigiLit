<?php
/**
 * Class xdglSearchFormGUI
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

class xdglSearchFormGUI extends ilPropertyFormGUI {

	use \xdgl\DIC;

	/**
	 * @var xdglSearchGUI
	 */
	protected $parent_gui;
	/**
	 * @var ilObjDigiLitAccess
	 */
	protected $access;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;

	/**
	 * xdglSearchFormGUI constructor.
	 *
	 * @param xdglSearchGUI $parent_gui
	 */
	public function __construct(xdglSearchGUI $parent_gui) {

		$this->parent_gui = $parent_gui;
		$this->access = new ilObjDigiLitAccess();
		$this->pl = ilDigiLitPlugin::getInstance();
		parent::__construct();
		$this->setFormAction($this->ctrl()->getFormAction($this->parent_gui));
		$this->initForm();
	}

	public function initForm() {
		$this->setTarget('_top');
		$this->setTitle($this->pl->txt('search_literature'));

		$ti = new ilTextInputGUI($this->pl->txt('author'), 'author');
		$ti->setRequired(true);
		$this->addItem($ti);

		$ti = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$ti->setRequired(true);
		$this->addItem($ti);

		$this->addCommandButton(xdglSearchGUI::CMD_SEARCH, $this->pl->txt('search'));
	}


}