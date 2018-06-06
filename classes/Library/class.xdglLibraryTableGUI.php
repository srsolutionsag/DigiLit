<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class xdglLibraryTableGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 *
 */
class xdglLibraryTableGUI extends ilTable2GUI {

	const TBL_XDGL_LIB_OVERVIEWS = 'tbl_xdgl_libr_overviews';
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $filter = array();


	/**
	 * @param xdglLibraryGUI $a_parent_obj
	 * @param string         $a_parent_cmd
	 */
	public function __construct(xdglLibraryGUI $a_parent_obj, $a_parent_cmd) {
		/**
		 * @var $ilCtrl    ilCtrl
		 * @var $ilToolbar ilToolbarGUI
		 */
		global $ilCtrl, $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->setId(self::TBL_XDGL_LIB_OVERVIEWS);
		$this->setPrefix(self::TBL_XDGL_LIB_OVERVIEWS);
		$this->setFormName(self::TBL_XDGL_LIB_OVERVIEWS);
		$this->ctrl->saveParameter($a_parent_obj, $this->getNavParameter());
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->parent_obj = $a_parent_obj;
		$this->setRowTemplate('tpl.lib_overview_row.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit');
		$this->setEnableNumInfo(true);
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->initColums();
		$this->initFilters();
		$this->setDefaultOrderField('title');
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->parseData();
		$ilToolbar->addButton($this->pl->txt('library_add'), $this->ctrl->getLinkTarget($this->parent_obj, xdglLibraryGUI::CMD_ADD), '', '', '',
			'emphatize');
		//		$this->addHeaderCommand($this->ctrl->getLinkTarget($this->parent_obj, xdglLibraryGUI::CMD_ADD), $this->pl->txt('library_add'));
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$obj = $this->objects[$a_set['id']];
		/**
		 * @var $obj xdglLibrary
		 */
		if ($obj->getIsPrimary()) {
			$this->tpl->setVariable('STYLE', 'font-weight: bold;');
		}
		$this->tpl->setVariable('VAL_ACTIVE', $obj->isActive());
		$this->tpl->setVariable('VAL_TITLE', $obj->getTitle());
		$this->tpl->setVariable('VAL_DESCRIPTION', $obj->getDescription());
		$this->tpl->setVariable('VAL_EMAIL', $obj->getEmail());

		$this->addActionMenu($a_set);
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglLibraryList = xdglLibrary::getCollection();
		if (!$this->getOrderField()) {
			$xdglLibraryList->orderBy('title', 'ASC');
		}
		$xdglLibraryList->orderBy($this->getOrderField(), $this->getOrderDirection());
		//		$xdglRequestList->where(array( 'digi_lit_object_id' => 0 ), '>');
		//		$xdglRequestList->where(array( 'status' => 0 ), '>');
		//		$xdglRequestList->innerjoin('usr_data', 'requester_usr_id', 'usr_id', array( 'email', 'firstname', 'lastname' ));
		//		foreach ($this->filter as $field => $value) {
		//			if ($value) {
		//				$xdglRequestList->where(array( $field => $value ));
		//			}
		//		}
		$this->setMaxCount($xdglLibraryList->count());
		if (!$xdglLibraryList->hasSets()) {
			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter');
		}
		$xdglLibraryList->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
		$xdglLibraryList->orderBy('title');
		$this->setData($xdglLibraryList->getArray());
		$this->objects = $xdglLibraryList->get();
	}


	protected function initColums() {
		$this->addColumn($this->pl->txt('library_active'));
		$this->addColumn($this->pl->txt('library_title'), 'title');
		$this->addColumn($this->pl->txt('library_description'), 'description');
		$this->addColumn($this->pl->txt('library_email'), 'email');
		$this->addColumn($this->pl->txt('library_actions'), 'actions');
	}


	/**
	 * @param $a_set
	 */
	protected function addActionMenu($a_set) {
		$obj = $this->objects[$a_set['id']];
		/**
		 * @var $obj xdglLibrary
		 */
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('common_actions'));

		$current_selection_list->setId(self::TBL_XDGL_LIB_OVERVIEWS . $a_set['id']);
		$current_selection_list->setUseImages(false);

		$this->ctrl->setParameter($this->parent_obj, xdglLibraryGUI::XDGL_LIB_ID, $a_set['id']);
		$this->ctrl->setParameterByClass('xdglLibrarianGUI', xdglLibrarianGUI::XDGL_LIBRARIAN_ID, $a_set['id']);
		$current_selection_list->addItem($this->pl->txt('library_view'), 'library_view',
			$this->ctrl->getLinkTarget($this->parent_obj, xdglLibraryGUI::CMD_VIEW));
		$current_selection_list->addItem($this->pl->txt('library_edit'), 'library_edit',
			$this->ctrl->getLinkTarget($this->parent_obj, xdglLibraryGUI::CMD_EDIT));
		$current_selection_list->addItem($this->pl->txt('library_assign'), 'library_assign',
			$this->ctrl->getLinkTargetByClass('xdglLibrarianGUI', xdglLibrarianGUI::CMD_ASSIGN));
		if ($obj->isDeletable()) {
			$current_selection_list->addItem($this->pl->txt('library_delete'), 'library_delete',
				$this->ctrl->getLinkTarget($this->parent_obj, xdglLibraryGUI::CMD_CONFIRM_DELETE));
		}

		$this->tpl->setVariable('VAL_ACTIONS', $current_selection_list->getHTML());
	}


	protected function initFilters() {
		// Status
		//		$te = new ilMultiSelectInputGUI($this->pl->txt('filter_status'), 'status');
		//		$te->setOptions(array(
		//			xdglRequest::STATUS_NEW => $this->pl->txt('library_status_' . xdglLibrary::STATUS_NEW),
		//			xdglLibrary::STATUS_IN_PROGRRESS => $this->pl->txt('library_status_' . xdglLibrary::STATUS_IN_PROGRRESS),
		//			xdglLibrary::STATUS_REFUSED => $this->pl->txt('library_status_' . xdglLibrary::STATUS_REFUSED),
		//			xdglLibrary::STATUS_RELEASED => $this->pl->txt('library_status_' . xdglLibrary::STATUS_RELEASED),
		//			xdglLibrary::STATUS_RELEASED => $this->pl->txt('library_status_' . xdglLibrary::STATUS_RELEASED),
		//			xdglLibrary::STATUS_COPY => $this->pl->txt('library_status_' . xdglLibrary::STATUS_COPY),
		//		));
		//		$this->addAndReadFilterItem($te);
	}


	/**
	 * @param $item
	 */
	protected function addAndReadFilterItem(ilFormPropertyGUI $item) {
		$this->addFilterItem($item);
		$item->readFromSession();
		$this->filter[$item->getPostVar()] = $item->getValue();
	}


	/**
	 * @param bool $a_in_determination
	 */
	public function resetOffset($a_in_determination = false) {
		parent::resetOffset(false);
		$this->ctrl->setParameter($this->parent_obj, $this->getNavParameter(), $this->nav_value);
	}
}


