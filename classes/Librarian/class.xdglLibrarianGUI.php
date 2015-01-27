<?php
//require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarianTableGUI.php');
//require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarianFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Form/class.xdglMultiUserInputGUI.php');
require_once('class.xdglLibrarian.php');

/**
 * Class xdglLibrarianGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_isCalledBy xdglLibrarianGUI : xdglMainGUI, xdglLibraryGUI
 */
class xdglLibrarianGUI {

	const XDGL_LIBRARIAN_ID = 'liba_id';
	const CMD_ASSIGN = 'assign';
	const CMD_UPDATEASSIGNEMENT = 'updateAssignements';
	const CMD_STANDARD = 'index';
	const CMD_GETAJAX = 'getAjaxData';
	const CMD_RETURN = 'returnToLibrary';
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var xdglLibrarian
	 */
	protected $librarian;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs_gui;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs;
		$this->tpl = $tpl;
		$this->tabs_gui = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->pl = ilDigiLitPlugin::getInstance();
	}


	/**
	 * @return bool
	 */
	public function executeCommand() {
		if (!ilObjDigiLitAccess::isAdmin()) {
			return false;
		}
		$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
		$this->ctrl->saveParameter($this, self::XDGL_LIBRARIAN_ID);
		$this->ctrl->saveParameter($this, xdglLibraryGUI::XDGL_LIB_ID);
		$this->ctrl->saveParameterByClass('xdglLibraryGUI', xdglLibraryGUI::XDGL_LIB_ID);
		switch ($cmd) {
			case self::CMD_ASSIGN:
			case self::CMD_GETAJAX:
			case self::CMD_RETURN:
			case self::CMD_UPDATEASSIGNEMENT:
				$this->tabs_gui->clearTargets();
				$this->tabs_gui->setBackTarget($this->pl->txt('librarian_back'), $this->ctrl->getLinkTargetByClass('xdglLibraryGUI'));
				$this->{$cmd}();
				break;
		}

		return true;
	}


	protected function assign() {
		$form = $this->buildForm();
		$user_ids = xdglLibrarian::where(array( 'library_id' => $_GET[xdglLibraryGUI::XDGL_LIB_ID] ))->getArray(NULL, 'usr_id');
		$form->setValuesByArray(array( 'usr_ids' => implode(',', $user_ids) ));
		$this->tpl->setContent($form->getHTML());
	}


	protected function updateAssignements() {
		$form = $this->buildForm();
		$form->checkInput();
		/**
		 * @var $obj xdglLibrarian
		 */
		$lib_id = $_GET[xdglLibraryGUI::XDGL_LIB_ID];

		$input = $form->getInput('usr_ids');
		$usr_ids = explode(',', $input[0]);
		foreach (xdglLibrarian::where(array( 'library_id' => $lib_id ))->where(array( 'usr_id' => $usr_ids ), 'NOT IN')->get() as $obj) {
			$obj->setActive(false);
			$obj->delete();
		}

		foreach ($usr_ids as $usr_id) {
			$obj = xdglLibrarian::findOrGetInstance($usr_id);
			$obj->setLibraryId($lib_id);
			$obj->setActive(true);
			if ($obj->is_new) {
				$obj->create();
			} else {
				$obj->update();
			}
		}
		ilUtil::sendSuccess($this->pl->txt('msg_success_add'), true);
		$this->returnToLibrary();
	}


	protected function returnToLibrary() {
		$this->ctrl->setParameterByClass('xdglLibraryGUI', xdglLibraryGUI::XDGL_LIB_ID, NULL);
		$this->ctrl->redirectByClass('xdglLibraryGUI');
	}


	protected function getAjaxData() {
		global $ilDB;
		/**
		 * @var ilDB $ilDB
		 */

		$term = $ilDB->quote('%' . $_GET['term'] . '%', 'text');
		$type = $ilDB->quote($_GET['container_type'], 'text');

		$query = "SELECT obj.obj_id, obj.title, usr.*
FROM object_data obj
				 JOIN usr_data usr ON usr.usr_id = obj.obj_id
			 WHERE obj.type = $type AND
				 (obj.title LIKE $term OR usr.firstname LIKE $term OR usr.lastname LIKE $term OR usr.email LIKE $term OR usr.login LIKE $term )
			 ORDER BY  obj.title";

		$res = $ilDB->query($query);
		$result = array();
		while ($row = $ilDB->fetchAssoc($res)) {
			$result[] = array( "id" => $row['obj_id'], "text" => $row['firstname'] . ' ' . $row['lastname'] . ' (' . $row['email'] . ')' );
		}
		echo json_encode($result);
		exit;
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function buildForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->pl->txt('librarian_form_title'));

		$xdglMultiUserInputGUI = new xdglMultiUserInputGUI($this->pl->txt('librarian_users'), 'usr_ids');
		$ajax_link = $this->ctrl->getLinkTarget($this, 'getAjaxData', '', true);
		$xdglMultiUserInputGUI->setAjaxLink($ajax_link);
		$form->addItem($xdglMultiUserInputGUI);

		$form->addCommandButton(self::CMD_UPDATEASSIGNEMENT, $this->pl->txt('librarian_update_assignements'));
		$form->addCommandButton(self::CMD_RETURN, $this->pl->txt('librarian_return'));

		return $form;
	}
}

?>
