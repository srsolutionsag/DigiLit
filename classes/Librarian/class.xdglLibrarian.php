<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/class.xdgl.php');
xdgl::initAR();
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php');

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
		return ! xdglRequest::where(array(
			'librarian_id' => $this->getUsrId(),
			'status' => array( xdglRequest::STATUS_REFUSED, xdglRequest::STATUS_RELEASED, xdglRequest::STATUS_COPY )
		), array( 'librarian_id' => '=', 'status' => 'NOT IN' ))->hasSets();
	}


	public function delete() {
		parent::delete(); // TODO: Change the autogenerated stub
	}


	/**
	 * @param       $usr_id
	 * @param       $library_id
	 *
	 * @return xdglLibrarian
	 */
	public static function find($usr_id, $library_id) {
		$obj = self::where(array( 'usr_id' => $usr_id, 'library_id' => $library_id ))->first();
		if ($obj instanceof xdglLibrarian) {
			return $obj;
		}

		return NULL;
	}


	/**
	 * @param       $usr_id
	 * @param       $library_id
	 *
	 * @return xdglLibrarian
	 */
	public static function findOrGetInstance($usr_id, $library_id) {
		$obj = self::find($usr_id, $library_id);
		if ($obj === NULL) {
			$obj = new self();
			$obj->setLibraryId($library_id);
			$obj->setUsrId($usr_id);
			$obj->is_new = true;
		}

		return $obj;
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
		if (! $all) {
			$list->where(array( 'library_id' => $lib_id ));
		}
		$list->innerjoin('usr_data', 'usr_id', 'usr_id', array( 'firstname', 'lastname', 'email' ));
		$list->concat(array( 'firstname', '" "', 'lastname', '" ("', 'email', '")"' ), 'user_fullname');
		if ($exclude) {
			$list->where(array( 'usr_id' => $exclude ), '!=');
		}

		$list->orderBy('user_fullname');

		return $list->getArray('usr_id', 'user_fullname');
	}


	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_sequence   true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
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


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
}

?>
