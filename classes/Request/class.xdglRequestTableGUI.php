<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('class.xdglRequestGUI.php');
require_once('class.xdglRequest.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/class.ilObjDigiLitListGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/class.ilDigiLitPlugin.php');
require_once('./Services/Table/classes/class.ilTable2GUI.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Notification/class.xdglNotification.php');
require_once('./Services/Form/classes/class.ilMultiSelectInputGUI.php');

/**
 * Class xdglRequestTableGUI
 *
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 *
 */
class xdglRequestTableGUI extends ilTable2GUI {

	const TBL_XDGL_REQUEST_OVERVIEWS = 'tbl_xdgl_request_overviews';
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $filter = array();


	/**
	 * @param xdglRequestGUI $a_parent_obj
	 * @param string         $a_parent_cmd
	 */
	public function  __construct(xdglRequestGUI $a_parent_obj, $a_parent_cmd) {
		/**
		 * @var $ilCtrl ilCtrl
		 */
		global $ilCtrl;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->setId(self::TBL_XDGL_REQUEST_OVERVIEWS);
		$this->setPrefix(self::TBL_XDGL_REQUEST_OVERVIEWS);
		$this->setFormName(self::TBL_XDGL_REQUEST_OVERVIEWS);
		$this->ctrl->saveParameter($a_parent_obj, $this->getNavParameter());
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->parent_obj = $a_parent_obj;
		$this->setRowTemplate('tpl.requests_overview_row.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit');
		$this->setEnableNumInfo(true);
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->initColums();
		$this->initFilters();
		$this->setDefaultOrderField('title');
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->parseData();
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$obj = xdglRequest::find($a_set['id']);
		$this->tpl->setVariable('VAL_EXT_ID', $obj->getExtId());
		$this->tpl->setVariable('VAL_TITLE', $a_set['title']);
		$this->tpl->setVariable('VAL_BOOK', $a_set['book']);
		$this->tpl->setVariable('VAL_PUBLISHING_YEAR', $a_set['publishing_year']);
		$this->tpl->setVariable('VAL_CREATE_DATE', $a_set['create_date']);
		$this->tpl->setVariable('VAL_LAST_UPDATE', $a_set['last_change']);
		$this->tpl->setVariable('VAL_REQUESTER_EMAIL', $a_set['usr_data_email']);
		$this->tpl->setVariable('VAL_STATUS', $this->pl->txt('request_status_' . $a_set['status']));
		$this->tpl->setVariable('VAL_LIBRARY', $a_set['xdgl_library_title']);
		$this->tpl->setVariable('VAL_LIBRARIAN', $a_set['usr_data_2_email']);

		$this->addActionMenu($a_set);
	}


	protected function initColums() {
		$this->addColumn($this->pl->txt('request_ext_id'), NULL);
		$this->addColumn($this->pl->txt('request_title'), 'title');
		$this->addColumn($this->pl->txt('request_book'), 'book');
		$this->addColumn($this->pl->txt('request_publishing_year'), 'publishing_year');
		$this->addColumn($this->pl->txt('request_creation_date'), 'create_date');
		$this->addColumn($this->pl->txt('request_date_last_status_change'), 'date_last_status_change');
		$this->addColumn($this->pl->txt('request_status'), 'status');
		$this->addColumn($this->pl->txt('request_requester_mailto'), 'usr_data_email');
		$this->addColumn($this->pl->txt('request_assigned_library'), 'xdgl_library_title');
		$this->addColumn($this->pl->txt('request_assigned_librarian'), 'usr_data_2_email');
		$this->addColumn($this->pl->txt('common_actions'));
	}


	/**
	 * @param $a_set
	 */
	protected function addActionMenu($a_set) {

		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('common_actions'));

		$current_selection_list->setId('request_overview_actions_' . $a_set['id']);
		$current_selection_list->setUseImages(false);

		// edit the request
		$this->ctrl->setParameter($this->parent_obj, xdglRequestGUI::XDGL_ID, $a_set['id']);
		$this->ctrl->setParameterByClass('xdglLibraryGUI', xdglRequestGUI::XDGL_ID, $a_set['id']);

		switch ($a_set['status']) {
			case xdglRequest::STATUS_NEW:
				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('upload_title'), 'upload_pdf', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_SELECT_FILE));
				$current_selection_list->addItem($this->pl->txt('request_change_status_to_wip'), 'change_status_to_wip', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_CHANGE_STATUS_TO_WIP));
				$current_selection_list->addItem($this->pl->txt('request_refuse'), 'refuse_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CDM_CONFIRM_REFUSE));
				$current_selection_list->addItem($this->pl->txt('request_assign'), 'assign_request', $this->ctrl->getLinkTargetByClass('xdglLibraryGUI', xdglLibraryGUI::CMD_ASSIGN_LIBRARY));
				break;
			case xdglRequest::STATUS_IN_PROGRRESS:
				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('upload_title'), 'upload_pdf', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_SELECT_FILE));
				$current_selection_list->addItem($this->pl->txt('request_refuse'), 'refuse_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CDM_CONFIRM_REFUSE));
				break;
			case xdglRequest::STATUS_RELEASED:
				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('request_replace_file'), 'request_replace_file', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_REPLACE_FILE));
				$current_selection_list->addItem($this->pl->txt('request_delete_file'), 'request_delete_file', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_DELETE_FILE));
				break;
			case xdglRequest::STATUS_REFUSED:
			case xdglRequest::STATUS_COPY:
				break;
		}

		$this->tpl->setVariable('VAL_ACTION', $current_selection_list->getHTML());
	}


	protected function initFilters() {
		// Status
		$te = new ilMultiSelectInputGUI($this->pl->txt('filter_status'), 'status');
		$te->setOptions(array(
			xdglRequest::STATUS_NEW => $this->pl->txt('request_status_' . xdglRequest::STATUS_NEW),
			xdglRequest::STATUS_IN_PROGRRESS => $this->pl->txt('request_status_' . xdglRequest::STATUS_IN_PROGRRESS),
			xdglRequest::STATUS_REFUSED => $this->pl->txt('request_status_' . xdglRequest::STATUS_REFUSED),
			xdglRequest::STATUS_RELEASED => $this->pl->txt('request_status_' . xdglRequest::STATUS_RELEASED),
			xdglRequest::STATUS_RELEASED => $this->pl->txt('request_status_' . xdglRequest::STATUS_RELEASED),
			xdglRequest::STATUS_COPY => $this->pl->txt('request_status_' . xdglRequest::STATUS_COPY),
		));
		$this->addAndReadFilterItem($te);

		// Library
		if (ilObjDigiLitAccess::showAllLibraries()) {
			$te = new ilMultiSelectInputGUI($this->pl->txt('filter_library'), 'xdgl_library_id');
			$te->setOptions(xdglLibrary::getArray('id', 'title'));
			$this->addAndReadFilterItem($te);
		}
		global $ilUser;
		$te = new ilMultiSelectInputGUI($this->pl->txt('filter_librarian'), 'xdgl_librarian_id');
		$lib_id = ilObjDigiLitAccess::showAllLibraries() ? NULL : xdglLibrary::isAssignedToLibrary($ilUser);
		$libs = xdglLibrarian::getAssignedLibrariansForLibrary($lib_id, $ilUser->getId(), ilObjDigiLitAccess::showAllLibraries());
		$libs[xdglRequest::LIBRARIAN_ID_NONE] = $this->pl->txt('filter_none');
		$libs[xdglRequest::LIBRARIAN_ID_MINE] = $this->pl->txt('filter_mine');
		ksort($libs);

		$te->setOptions($libs);
		$this->addAndReadFilterItem($te);
	}


	/**
	 * @param                  $usr_id
	 * @param ActiveRecordList $xdglRequestList
	 *
	 * @throws Exception
	 */
	protected function filterResults($usr_id, ActiveRecordList $xdglRequestList) {
		foreach ($this->filter as $field => $value) {
			if ($value) {
				switch ($field) {
					case 'xdgl_library_id':
						$field = 'xdgl_library.id';
						$xdglRequestList->where(array( $field => $value ));
						break;
					case 'xdgl_librarian_id':
						$key = array_keys($value, xdglRequest::LIBRARIAN_ID_MINE);
						if ($key) {
							$value[$key] = $usr_id;
						}

						$xdglRequestList->where(array( 'librarian_id' => $value ));
						break;
					default:
						$xdglRequestList->where(array( $field => $value ));
						break;
				}
			}
		}
	}


	protected function parseData() {
		global $ilUser;
		$usr_id = $ilUser->getId();
		/**
		 * @var $ilUser ilObjUser
		 */
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = xdglRequest::getCollection();
		$xdglRequestList->orderBy($this->getOrderField(), $this->getOrderDirection());
		$xdglRequestList->where(array( 'digi_lit_object_id' => 0 ), '>');
		$xdglRequestList->where(array( 'status' => 0 ), '>');
		$xdglRequestList->leftjoin('usr_data', 'requester_usr_id', 'usr_id', array( 'email' ));
		$xdglRequestList->leftjoin(xdglLibrary::TABLE_NAME, 'library_id', 'id', array( 'id', 'title' ));
		$xdglRequestList->leftjoin(xdglLibrarian::TABLE_NAME, 'librarian_id', 'usr_id', array( 'usr_id', 'library_id' ));
		$xdglRequestList->leftjoin('usr_data', 'librarian_id', 'usr_id', array( 'email' ));
		if (!ilObjDigiLitAccess::showAllLibraries()) {
			/**
			 * @var xdglLibrarian $xdglLibrarian
			 */
			$xdglLibrarian = xdglLibrarian::find($ilUser->getId());
			if ($xdglLibrarian instanceof xdglLibrarian) {
				$xdglRequestList->where(array( 'xdgl_library.id' => $xdglLibrarian->getLibraryId() ), '=');
			}
		}

		$this->filterResults($usr_id, $xdglRequestList);
		$this->setMaxCount($xdglRequestList->count());
		if (!$xdglRequestList->hasSets()) {
			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter');
		}
		$xdglRequestList->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
		$xdglRequestList->dateFormat('d.m.Y - H:i:s');
		//		$xdglRequestList->debug();
		$this->setData($xdglRequestList->getArray());
	}


	/**
	 * @param $item
	 */
	protected function addAndReadFilterItem(ilFormPropertyGUI $item) {
		$this->addFilterItem($item);
		$item->readFromSession();
		if ($item instanceof ilCheckboxInputGUI) {
			$this->filter[$item->getPostVar()] = $item->getChecked();
		} else {
			$this->filter[$item->getPostVar()] = $item->getValue();
		}
	}


	function resetOffset() {
		parent::resetOffset(false);
		$this->ctrl->setParameter($this->parent_obj, $this->getNavParameter(), $this->nav_value);
	}
	/**
	 * @param $usr_id
	 * @param $xdglRequestList
	 */

}

?>
