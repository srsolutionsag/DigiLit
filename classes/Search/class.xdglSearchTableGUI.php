<?php

use srag\DIC\DigiLit\DICTrait;

/**
 * Class xdglSearchTableGUI
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
class xdglSearchTableGUI extends ilTable2GUI {
	use DICTrait;

	const TBL_ID = 'xdgl_search';
	/**
	 * @var array
	 */
	protected $filter = [];
	/**
	 * @var xdglSearchGUI
	 */
	protected $parent_obj;
	/**
	 * @var ilObjDigiLitAccess
	 */
	protected $access;
	/**
	 * @var ilDigiLitPlugin
	 */
	protected $pl;
	protected $tpl;


	/**
	 * ilLocationDataTableGUI constructor.
	 *
	 * @param xdglSearchGUI $a_parent_obj
	 * @param string        $a_parent_cmd
	 * @param string        $search_title
	 * @param string        $search_author
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_template_context = "", $search_title, $search_author) {

		$this->parent_obj = $a_parent_obj;
		$this->access = new ilObjDigiLitAccess();
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->setId(self::TBL_ID);
		$this->setPrefix(self::TBL_ID);
		$this->setFormName(self::TBL_ID);
		$this->tpl = self::dic()->ui()->mainTemplate();
        self::dic()->ctrl()->saveParameter($a_parent_obj, $this->getNavParameter());

		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);
		$this->parent_obj = $a_parent_obj;
		$this->setRowTemplate("tpl.search_row.html", "Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit");

		//TODO: based on previous cmd change formactionbyclass
		//$this->setFormAction(self::dic()->ctrl()->getFormActionByClass(ilObjDigiLitGUI::class));
		$this->setFormAction(self::dic()->ctrl()->getFormActionByClass(xdglSearchGUI::class));

		$this->setExternalSorting(true);
		$this->addCommandButton(xdglSearchGUI::CMD_ADD_LITERATURE, $this->pl->txt('add_literature'));

		$this->setDefaultOrderField("title");
		$this->setDefaultOrderDirection("asc");
		$this->setExternalSegmentation(true);
		$this->setEnableHeader(true);

		$this->initColums();
		$this->parseData($search_title ,$search_author);
	}

	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$rdg_input = new ilRadioGroupInputGUI('', 'chosen_literature');
		$rd_option = new ilRadioOption('', $a_set['id']);
		$rdg_input->addOption($rd_option);
		$this->tpl->setVariable('RADIO_BTN', $rdg_input->render());
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode(", ", $a_set[$k]) : $a_set[$k]));
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
			$this->tpl->setVariable('REQUEST_ID', $a_set['id']);
		}
	}


	protected function initColums() {
		$number_of_selected_columns = count($this->getSelectedColumns());
		//add one to the number of columns for the radio button
		$number_of_selected_columns ++;
		$column_width = 100 / $number_of_selected_columns . '%';

		//add column for radio buttons
		$this->addColumn('', '', $column_width);
		$all_cols = $this->getSelectableColumns();
		foreach ($this->getSelectedColumns() as $col) {
			$this->addColumn($all_cols[$col]['txt'], $col, $column_width);
		}
	}


	/**
	 * @param string    $search_title
	 * @param string    $search_author
	 */
	protected function parseData($search_title, $search_author) {
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$data = xdglRequest::findDistinctRequestsByTitleAndAuthor($search_title, $search_author, $this->limit);
		$count = count($data);

		$this->setMaxCount($count);
		$this->setData($data);
	}


	public function getSelectableColumns() {
		$cols["status"] = array(
			"txt" => $this->pl->txt("request_status"),
			"default" => true
		);
		$cols["author"] = array(
			"txt" => $this->pl->txt("author"),
			"default" => true
		);
		$cols["title"] = array(
			"txt" => $this->pl->txt("title"),
			"default" => true
		);
		$cols["book"] = array(
			"txt" => $this->pl->txt("request_book"),
			"default" => true
		);
		$cols["publisher"] = array(
			"txt" => $this->pl->txt("request_publisher"),
			"default" => true
		);
		$cols["location"] = array(
			"txt" => $this->pl->txt("request_location"),
			"default" => true
		);
		$cols["publishing_year"] = array(
			"txt" => $this->pl->txt("request_publishing_year"),
			"default" => true
		);
		$cols["pages"] = array(
			"txt" => $this->pl->txt("request_pages"),
			"default" => true
		);
		return $cols;
	}
}