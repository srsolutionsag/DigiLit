<?php

use srag\DIC\DigiLit\DICTrait;

/**
 * Class xdglSearchFormGUI
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
class xdglSearchFormGUI extends ilPropertyFormGUI
{

    use DICTrait;

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
    public function __construct(xdglSearchGUI $parent_gui)
    {

        $this->parent_gui = $parent_gui;
        $this->access = new ilObjDigiLitAccess();
        $this->pl = ilDigiLitPlugin::getInstance();
        parent::__construct();
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));
        $this->initForm();
    }

    public function initForm()
    {
        $this->setTarget('_top');
        $this->setTitle($this->pl->txt('search_literature'));

        $ti = new ilTextInputGUI($this->pl->txt('author'), 'author');
        $this->addItem($ti);

        $ti = new ilTextInputGUI($this->pl->txt('title'), 'title');
        $this->addItem($ti);

        $this->addCommandButton(xdglSearchGUI::CMD_SEARCH, $this->pl->txt('search'));
    }

}
