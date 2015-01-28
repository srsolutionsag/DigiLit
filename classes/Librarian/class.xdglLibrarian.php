<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php');
require_once('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php');

/**
 * Class xdglLibrarian
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglLibrarian extends ActiveRecord {

	const TABLE_NAME = 'xdgl_librarian';


	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 * @deprecated
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return bool
	 */
	public function isDeletable() {
		return !xdglRequest::where(array( 'librarian_id' => $this->getUsrId() ))->hasSets();
	}


	/**
	 * @param null $lib_id
	 *
	 * @return array
	 */
	public static function getAssignedLibrariansForLibrary($lib_id = NULL, $exclude = NULL, $all = false) {
		if ($lib_id === NULL) {
			$lib_id = xdglLibrary::getPrimaryId();
		}
		$list = self::getCollection();
		if (!$all) {
			$list->where(array( 'library_id' => $lib_id ));
		}
		$list->innerjoin('usr_data', 'usr_id', 'usr_id', array( 'firstname', 'lastname', 'email' ));
		$list->concat(array( 'firstname', '" "', 'lastname', '" ("', 'email', '")"' ), 'user_fullname');
		if ($exclude) {
			$list->where(array( 'usr_id' => $exclude ), '!=');
		}

		return $list->getArray('usr_id', 'user_fullname');
	}


	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $usr_id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $library_id = 0;
	/**
	 * @var bool
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $active = true;


	/**
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}


	/**
	 * @return int
	 */
	public function getLibraryId() {
		return $this->library_id;
	}


	/**
	 * @param int $library_id
	 */
	public function setLibraryId($library_id) {
		$this->library_id = $library_id;
	}


	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->active;
	}


	/**
	 * @param boolean $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}
}

?>
