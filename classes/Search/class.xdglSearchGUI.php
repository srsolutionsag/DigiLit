<?php

use srag\DIC\DigiLit\DICTrait;
use srag\Plugins\DigiLit\Interfaces\Facade\ilObjDigiLitFacadeInterface;

/**
 * Class xdglSearchGUI
 *
 * @ilCtrl_isCalledBy xdglSearchGUI: ilRepositoryGUI, ilObjDigiLitGUI
 *
 * @author            : Benjamin Seglias   <bs@studer-raimann.ch>
 */
class xdglSearchGUI
{
    use DICTrait;

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
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilObjDigiLitFacadeInterface
     */
    protected $ilObjDigiLitFacadeFactory;

    public function __construct()
    {
        $this->access = new ilObjDigiLitAccess();
        $this->pl = ilDigiLitPlugin::getInstance();
        $this->ctrl = self::dic()->ctrl();
        $this->ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
    }

    public function executeCommand()
    {
        $nextClass = self::dic()->ctrl()->getNextClass();
        switch ($nextClass) {
            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_STANDARD);
                $this->{$cmd}();
                break;
        }
    }

    protected function searchForm()
    {
        $xdglSearchFormGUI = new xdglSearchFormGUI($this);
        self::dic()->ui()->mainTemplate()->setContent($xdglSearchFormGUI->getHTML());
        $this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::PLUGIN_ID);
        $this->ctrl->setParameterByClass(ilObjDigiLitGUI::class, 'new_type', ilDigiLitPlugin::PLUGIN_ID);
        $this->ctrl->setParameter($this, 'previous_cmd', self::CMD_STANDARD);
        $this->ctrl->saveParameter($this, 'ref_id');
    }

    protected function search()
    {
        $xdglSearchFormGUI = new xdglSearchFormGUI($this);
        $xdglSearchFormGUI->setValuesByPost();
        $xdglSearchTableGUI = new xdglSearchTableGUI($this, self::CMD_STANDARD, "", $_POST['title'], $_POST['author']);
        $ilObjDigiLitGUI = new ilObjDigiLitGUI();
        $this->ctrl->setParameter($this, 'new_type', ilDigiLitPlugin::PLUGIN_ID);
        $this->ctrl->setParameterByClass(ilObjDigiLitGUI::class, 'new_type', ilDigiLitPlugin::PLUGIN_ID);
        $this->ctrl->setParameter($this, 'previous_cmd', self::CMD_SEARCH);
        $this->ctrl->saveParameterByClass(ilObjDigiLitGUI::class, 'ref_id');
        if (!empty($xdglSearchTableGUI->row_data)) {
            self::dic()->ui()->mainTemplate()->setContent($this->getAccordion(array($xdglSearchFormGUI,
                                                                                    $ilObjDigiLitGUI->initCreateForm('new')
            ), $xdglSearchTableGUI));
        } else {
            self::dic()->ui()->mainTemplate()->setContent($this->getAccordion(array($xdglSearchFormGUI,
                                                                                    $ilObjDigiLitGUI->initCreateForm('new')
            )));
        }

    }

    protected function getAccordion($a_forms, xdglSearchTableGUI $a_search_table = null)
    {
        $acc = new ilAccordionGUI();
        $acc->setBehaviour(ilAccordionGUI::FIRST_OPEN);
        $cnt = 1;
        foreach ($a_forms as $form_type => $cf) {
            $htpl = new ilTemplate("tpl.creation_acc_head.html", true, true, "Services/Object");

            // using custom form titles (used for repository plugins)
            $form_title = "";
            if (method_exists($this, "getCreationFormTitle")) {
                $form_title = $this->getCreationFormTitle($form_type);
            }
            if (!$form_title) {
                $form_title = $cf->getTitle();
            }

            // move title from form to accordion
            $htpl->setVariable("TITLE", self::dic()->language()->txt("option") . " " . $cnt . ": " .
                $form_title);
            $cf->setTitle(null);
            $cf->setTitleIcon(null);
            $cf->setTableWidth("100%");

            if ($cf instanceof xdglSearchFormGUI && !empty($a_search_table)) {
                $acc->addItem($htpl->get(), $cf->getHTML() . $a_search_table->getHTML());
            } else {
                $acc->addItem($htpl->get(), $cf->getHTML());
            }
            $cnt++;
        }

        return "<div class='ilCreationFormSection'>" . $acc->getHTML() . "</div>";
    }

    protected function addLiterature()
    {
        $oldRequestId = $_POST['chosen_literature'];
        $oldRequest = xdglRequest::find($oldRequestId);
        $ilObjDigiLit = new ilObjDigiLit();
        $ilObjDigiLit->setType('xdgl');
        $ilObjDigiLit->setTitle($oldRequest->getTitle());
        $ilObjDigiLit->create();
        $ilObjectDigiLitGUI = new ilObjDigiLitGUI();
        $ilObjectDigiLitGUI->putObjectInTree($ilObjDigiLit, $ilObjDigiLit::returnParentCrsRefId($_GET['ref_id']));
        //pass $oldRequestId as array to afterSave in order that in afterSave the function function_get_args behaves correctly in this case as well as in the case a new xdlgRequest was created
        $oldRequestIdArray = array($oldRequestId);
        $ilObjectDigiLitGUI->afterSave($ilObjDigiLit, $oldRequestIdArray);
    }
}
