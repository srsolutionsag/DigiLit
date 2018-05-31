<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class xdglRequestTableGUI
 *
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 *
 */
class xdglRequestTableGUI extends ilTable2GUI {

	const TBL_XDGL_REQUEST_OVERVIEWS = 'tbl_xdgl_request_overviews_v2';
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
	public function __construct(xdglRequestGUI $a_parent_obj, $a_parent_cmd) {
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
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->initColums();
		$this->initFilters();
		$this->setDefaultOrderField('title');
		$this->setEnableNumInfo(true);
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->parseData();
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$obj = xdglRequest::find($a_set['id']);
		//		$a_set['ext_id'] = $obj->getExtId();
		$this->tpl->setVariable('VAL_EXT_ID', $a_set['ext_id']);

		$this->tpl->setVariable('VAL_TITLE', $a_set['title']);
		$this->tpl->setVariable('VAL_BOOK', $a_set['book']);
		$this->tpl->setVariable('VAL_PUBLISHING_YEAR', $a_set['publishing_year']);
		$this->tpl->setVariable('VAL_CREATE_DATE', $a_set['create_date']);
		$this->tpl->setVariable('VAL_LAST_UPDATE', $a_set['last_change']);
		$this->tpl->setVariable('VAL_REQUESTER_EMAIL', $a_set['usr_data_email']);
		$this->tpl->setVariable('VAL_STATUS', $this->pl->txt('request_status_' . $a_set['status']));
		$this->tpl->setVariable('VAL_LIBRARY', $a_set['xdgl_library_title']);
		$this->tpl->setVariable('VAL_LIBRARIAN', $a_set['usr_data_2_email']);
		$this->tpl->setVariable('VAL_NUMBER_OF_USAGES', $a_set['number_of_usages']);

		$this->addActionMenu($a_set);
	}


	protected function initColums() {
		//		$this->addColumn($this->pl->txt('request_ext_id'), NULL);
		$this->addColumn($this->pl->txt('request_ext_id'), 'ext_id');
		$this->addColumn($this->pl->txt('request_title'), 'title');
		$this->addColumn($this->pl->txt('request_book'), 'book');
		$this->addColumn($this->pl->txt('request_publishing_year'), 'publishing_year');
		$this->addColumn($this->pl->txt('request_creation_date'), 'create_date');
		$this->addColumn($this->pl->txt('request_date_last_status_change'), 'date_last_status_change');
		$this->addColumn($this->pl->txt('request_status'), 'status');
		$this->addColumn($this->pl->txt('request_requester_mailto'), 'usr_data_email');
		//		if (xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES)) {
		$this->addColumn($this->pl->txt('request_assigned_library'), 'xdgl_library_title');
		$this->addColumn($this->pl->txt('request_assigned_librarian'), 'usr_data_2_email');
		$this->addColumn($this->pl->txt('number_of_usages'), 'number_of_usages');
		//		}
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
			case xdglRequest::STATUS_IN_PROGRRESS:
				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('upload_title'), 'upload_pdf',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_SELECT_FILE));
				$current_selection_list->addItem($this->pl->txt('request_change_status_to_wip'), 'change_status_to_wip',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_CHANGE_STATUS_TO_WIP));
				$current_selection_list->addItem($this->pl->txt('request_refuse'), 'refuse_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CDM_CONFIRM_REFUSE));
				$current_selection_list->addItem($this->pl->txt('request_assign'), 'assign_request',
					$this->ctrl->getLinkTargetByClass('xdglLibraryGUI', xdglLibraryGUI::CMD_ASSIGN_LIBRARY));
				$current_selection_list->addItem($this->pl->txt('delete_request'), 'delete_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_DELETE_REQUEST));
				break;
			//			case xdglRequest::STATUS_IN_PROGRRESS:
			//				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
			//				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
			//				$current_selection_list->addItem($this->pl->txt('upload_title'), 'upload_pdf', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_SELECT_FILE));
			//				$current_selection_list->addItem($this->pl->txt('request_refuse'), 'refuse_request', $this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CDM_CONFIRM_REFUSE));
			//				break;
			case xdglRequest::STATUS_RELEASED:
				$current_selection_list->addItem($this->pl->txt('request_view'), 'view_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_VIEW));
				$current_selection_list->addItem($this->pl->txt('request_edit'), 'edit_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('request_download_file'), 'request_download_file',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_DOWNLOAD_FILE));
				$current_selection_list->addItem($this->pl->txt('request_replace_file'), 'request_replace_file',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_REPLACE_FILE));
				$current_selection_list->addItem($this->pl->txt('request_delete_file'), 'request_delete_file',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_DELETE_FILE));
				$current_selection_list->addItem($this->pl->txt('delete_request'), 'delete_request',
					$this->ctrl->getLinkTarget($this->parent_obj, xdglRequestGUI::CMD_DELETE_REQUEST));
				break;
			case xdglRequest::STATUS_REFUSED:
				break;
		}

		$this->tpl->setVariable('VAL_ACTION', $current_selection_list->getHTML());
	}


	protected function initFilters() {
		// Status
		$te = new ilMultiSelectInputGUI($this->pl->txt('filter_status'), 'status');
		$te->setOptions(array(
			xdglRequest::STATUS_NEW          => $this->pl->txt('request_status_' . xdglRequest::STATUS_NEW),
			xdglRequest::STATUS_IN_PROGRRESS => $this->pl->txt('request_status_' . xdglRequest::STATUS_IN_PROGRRESS),
			xdglRequest::STATUS_REFUSED      => $this->pl->txt('request_status_' . xdglRequest::STATUS_REFUSED),
			xdglRequest::STATUS_RELEASED     => $this->pl->txt('request_status_' . xdglRequest::STATUS_RELEASED),
			xdglRequest::STATUS_RELEASED     => $this->pl->txt('request_status_' . xdglRequest::STATUS_RELEASED),
		));
		$this->addAndReadFilterItem($te);

		// Library
		if (ilObjDigiLitAccess::showAllLibraries()) {
			$te = new ilMultiSelectInputGUI($this->pl->txt('filter_library'), 'xdgl_library_id');
			$te->setOptions(xdglLibrary::where(array('active' => true))->getArray('id', 'title'));
			$this->addAndReadFilterItem($te);
		}
		global $ilUser;
		$te = new ilMultiSelectInputGUI($this->pl->txt('filter_librarian'), 'xdgl_librarian_id');
		xdglLibrary::getLibraryIdsForUser($ilUser);
		$lib_id = ilObjDigiLitAccess::showAllLibraries() ? null : xdglLibrary::getLibraryIdsForUser($ilUser);
		$libs = xdglLibrarian::getAssignedLibrariansForLibrary($lib_id, $ilUser->getId(), ilObjDigiLitAccess::showAllLibraries());
		$libs[xdglRequest::LIBRARIAN_ID_NONE] = $this->pl->txt('filter_none');
		$libs[xdglRequest::LIBRARIAN_ID_MINE] = $this->pl->txt('filter_mine');
		ksort($libs);
		$te->setOptions($libs);
		$this->addAndReadFilterItem($te);

		// Ext_ID
		$te = new ilTextInputGUI($this->pl->txt('request_ext_id'), 'ext_id');
		$this->addAndReadFilterItem($te);

		// number of usages
		$select = new ilSelectInputGUI($this->pl->txt('number_of_usages'), 'number_of_usages');
		$select->setOptions(array('0-5' => '0-5', '5-10' => '5-10', '10-15' => '10-15', '15-20' => '15-20'));
		$this->addAndReadFilterItem($select);
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
						$xdglRequestList->where(array($field => $value));
						break;
					case 'ext_id':
						//						$xdglRequestList->where(array( $field => $value ), 'LIKE');
						$h = new arHaving();
						$h->setFieldname('ext_id');
						$h->setValue('%' . $value . '%');
						$h->setOperator('LIKE');
						$xdglRequestList->getArHavingCollection()->add($h);
						break;
					case 'xdgl_librarian_id':
						$key = array_keys($value, xdglRequest::LIBRARIAN_ID_MINE);
						if (count($key)) {
							$value[$key[0]] = $usr_id;
						}
						$xdglRequestList->where(array('librarian_id' => $value));
						break;
					case 'number_of_usages':
						$start_between = substr($value, 0, strpos($value, '-'));
						$end_between = str_replace($start_between . '-', '', $value);
						$xdglRequestList->where('number_of_usages'  . ' BETWEEN ' . $start_between . ' AND ' . $end_between);
						break;
					default:
						$xdglRequestList->where(array($field => $value));
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
		//$xdglRequestList->where(array('digi_lit_object_id' => 0), '>');
		$xdglRequestList->where(array('status' => 0), '>');
		$xdglRequestList->leftjoin('usr_data', 'requester_usr_id', 'usr_id', array('email'));
		$xdglRequestList->leftjoin(xdglLibrary::TABLE_NAME, 'library_id', 'id', array('id', 'title'));
		$xdglRequestList->leftjoin(xdglLibrarian::TABLE_NAME, 'librarian_id', 'usr_id', array('usr_id', 'library_id'));
		$xdglRequestList->leftjoin('usr_data', 'librarian_id', 'usr_id', array('email'));
		$xdglRequestList->leftjoin('xdgl_request_usage', 'id', 'request_id', array( 'crs_ref_id'), '=');
		$xdglRequestList->leftjoin('object_reference', 'xdgl_request_usage.crs_ref_id', 'ref_id', array( 'ref_id', 'obj_id' ), '=', true);
		$xdglRequestList->leftjoin('object_data', 'object_reference.obj_id', 'obj_id', array( 'title' ), '=', true);

		// Ext_ID
		$sel = new arSelect();
		$sel->setAs('ext_id');
		if (xdglConfig::hasValidRegex()) {
			$regex = xdglConfig::getConfigValue(xdglConfig::F_REGEX);
			preg_match('/\/\((.*)\)\//', $regex, $matches);
			$sel->setFieldName('CASE object_data.title REGEXP "' . $matches[1] . '"
				WHEN "1" THEN CONCAT(SUBSTRING_INDEX(object_data.title, " ", 1), "-", LPAD(xdgl_request.id, 6, 0))
				WHEN "0" THEN CONCAT("UNKNOWN-", LPAD(xdgl_request.id, 6, 0)) END');
			$sel->setTableName('');
		} else {
			$sel->setFieldName('id');
			$sel->setTableName('xdgl_request');
		}
		$xdglRequestList->getArSelectCollection()->add($sel);

		// number_of_usages
		$sel = new arSelect();
		$sel->setAs('number_of_usages');
		$sel->setFieldName('COUNT(xdgl_request_usage.id)');
		$xdglRequestList->getArSelectCollection()->add($sel);

		if (!ilObjDigiLitAccess::showAllLibraries()) {
			$lib_ids = xdglLibrary::getLibraryIdsForUser($ilUser);
			$xdglRequestList->where(array('xdgl_library.id' => $lib_ids));
		}

		$this->filterResults($usr_id, $xdglRequestList);
		$this->setMaxCount($xdglRequestList->count());
		if (!$xdglRequestList->hasSets()) {
			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter');
		}
		$xdglRequestList->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
		$xdglRequestList->dateFormat('d.m.Y - H:i:s');

		$a_data = $xdglRequestList->getArray();

		$this->setData($a_data);
	}


	/**
	 * @param $item
	 */
	protected function addAndReadFilterItem(ilFormPropertyGUI $item) {
		$this->addFilterItem($item);
		$item->readFromSession();
		if ($item instanceof ilCheckboxInputGUI) {
			$this->filter[$item->getPostVar()] = $item->getChecked();
		} elseif($item instanceof ilSelectInputGUI && $item->getPostVar() == 'number_of_usages') {
			if(isset($_POST['number_of_usages'])) {
				$item->setValue($_POST['number_of_usages']);
			}
			$this->filter[$item->getPostVar()] = $item->getValue();
		} else {
			$this->filter[$item->getPostVar()] = $item->getValue();
		}
	}


	//	public function resetOffset() {
	//		parent::resetOffset(false);
	//		$this->ctrl->setParameter($this->parent_obj, $this->getNavParameter(), $this->nav_value);
	//	}
}


