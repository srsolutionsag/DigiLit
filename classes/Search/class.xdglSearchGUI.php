<?php
/**
 * Class xdglSearchGUI
 *
 * @ilCtrl_isCalledBy xdglSearchGUI: ilRepositoryGUI, ilObjDigiLitGUI
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */

class xdglSearchGUI {
	use \xdgl\DIC;

	const CMD_STANDARD = 'searchForm';
	const CMD_SEARCH = 'search';
	const CMD_ADD_LITERATURE = 'addLiterature';

	/**
	 * @var ilObjDigiLitAccess
	 */
	protected $access;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	protected $ctrl;


	public function __construct() {
		$this->access = new ilObjDigiLitAccess();
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->ctrl = $this->ctrl();
	}

	public function executeCommand() {
		$nextClass = $this->ctrl()->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl()->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}

	protected function searchForm() {
			$xdglSearchFormGUI = new xdglSearchFormGUI($this);
			$this->tpl()->setContent($xdglSearchFormGUI->getHTML());
			$this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::XDGL);
			$this->ctrl->setParameterByClass(ilObjDigiLitGUI::class, 'new_type', ilDigiLitPlugin::XDGL);
			$this->ctrl->setParameter($this, 'previous_cmd', self::CMD_STANDARD);
			$this->ctrl->saveParameter($this, 'ref_id');
			//return $xdglSearchFormGUI->getHTML();
	}

	protected function search() {
		$xdglSearchFormGUI = new xdglSearchFormGUI($this);
		$xdglSearchFormGUI->setValuesByPost();
		$xdglSearchTableGUI = new xdglSearchTableGUI($this, self::CMD_STANDARD, "", $_POST['title'], $_POST['author']);
		$ilObjDigiLitGUI = new ilObjDigiLitGUI();
		$this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::XDGL);
		$this->ctrl->setParameterByClass(ilObjDigiLitGUI::class, 'new_type', ilDigiLitPlugin::XDGL);
		$this->ctrl->setParameter($this, 'previous_cmd', self::CMD_SEARCH);
		$this->ctrl->saveParameterByClass(ilObjDigiLitGUI::class, 'ref_id');
		//$this->tpl()->setContent($xdglSearchFormGUI->getHTML() . $xdglSearchTableGUI->getHTML() . $ilObjDigiLitGUI->initCreateForm('new')->getHTML());
		if(!empty($xdglSearchTableGUI->row_data)) {
			$this->tpl()->setContent($this->getAccordion(array($xdglSearchFormGUI, $ilObjDigiLitGUI->initCreateForm('new')), $xdglSearchTableGUI));
		} else {
			$this->tpl()->setContent($this->getAccordion(array($xdglSearchFormGUI, $ilObjDigiLitGUI->initCreateForm('new'))));
		}

	}

	protected function getAccordion($a_forms, xdglSearchTableGUI $a_search_table = null) {
		include_once("./Services/Accordion/classes/class.ilAccordionGUI.php");

		$acc = new ilAccordionGUI();
		$acc->setBehaviour(ilAccordionGUI::FIRST_OPEN);
		$cnt = 1;
		foreach ($a_forms as $form_type => $cf)
		{
			$htpl = new ilTemplate("tpl.creation_acc_head.html", true, true, "Services/Object");

			// using custom form titles (used for repository plugins)
			$form_title = "";
			if(method_exists($this, "getCreationFormTitle"))
			{
				$form_title = $this->getCreationFormTitle($form_type);
			}
			if(!$form_title)
			{
				$form_title = $cf->getTitle();
			}

			// move title from form to accordion
			$htpl->setVariable("TITLE", $this->lng()->txt("option")." ".$cnt.": ".
				$form_title);
			$cf->setTitle(null);
			$cf->setTitleIcon(null);
			$cf->setTableWidth("100%");

			if($cf instanceof xdglSearchFormGUI && !empty($a_search_table)) {
				$acc->addItem($htpl->get(), $cf->getHTML() . $a_search_table->getHTML());
			} else {
				$acc->addItem($htpl->get(), $cf->getHTML());
			}
			$cnt++;
		}

		return "<div class='ilCreationFormSection'>".$acc->getHTML()."</div>";
	}

	protected function addLiterature() {
		$oldRequestId = $_POST['chosen_literature'];
		$ilObjDigiLit = new ilObjDigiLit();
		$oldRequest = xdglRequest::find($oldRequestId);
		$oldIlObDigiLit_rec = $ilObjDigiLit::getObjectById($oldRequest->getDigiLitObjectId());
		$ilObjDigiLit->setType('xdgl');
		$ilObjDigiLit->setTitle($oldIlObDigiLit_rec['title']);
		$ilObjDigiLit->setDescription($oldIlObDigiLit_rec['description']);
		$ilObjDigiLit->create();
		$ilObjectDigiLitGUI = new ilObjDigiLitGUI();
		$ilObjectDigiLitGUI->putObjectInTree($ilObjDigiLit, $ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']));
		$newXdglRequest = xdglRequest::copyRequest($oldRequest, $ilObjDigiLit->getId());
		//status has to be released to download the file
		$newXdglRequest->setStatus(xdglRequest::STATUS_RELEASED);
		$newXdglRequest->update();
		$ilObjectDigiLitGUI->afterSave($ilObjDigiLit, $newXdglRequest->getId());
	}
}