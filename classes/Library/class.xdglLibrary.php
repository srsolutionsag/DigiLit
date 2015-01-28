<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php');
require_once('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php');

/**
 * Class xdglLibrary
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglLibrary extends ActiveRecord {

	const TABLE_NAME = 'xdgl_library';
	/**
	 * @var int
	 */
	protected $not_deletable_reason;
	/**
	 * @var xdglLibrarian
	 */
	protected $librarians = [ ];
	/**
	 * @var int
	 */
	protected $assigned_requests_count = 0;
	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 * @con_sequence   true
	 */
	protected $id = '';
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 * @db_is_notnull       true
	 */
	protected $active = false;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $title = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	protected $description = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $ext_id = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	protected $email = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $is_primary = false;


	/**
	 * @return int
	 */
	public static function getPrimaryId() {
		return self::getPrimary()->getId();
	}


	/**
	 * @return xdglLibrary
	 */
	public static function getPrimary() {
		/**
		 * @var $res xdglLibrary
		 */
		return self::where(array( 'is_primary' => 1 ))->first();
	}


	/**
	 * @param ilObjUser $ilObjUser
	 *
	 * @return ActiveRecord|xdglLibrary
	 */
	public static function getLibraryForUser(ilObjUser $ilObjUser) {
		$xdglLibrarian = xdglLibrarian::find($ilObjUser->getId());
		if ($xdglLibrarian instanceof xdglLibrarian) {
			$libraryId = $xdglLibrarian->getLibraryId();

			return self::find($libraryId);
		}

		return self::getPrimary();
	}


	/**
	 * @param ilObjUser $ilObjUser
	 *
	 * @return bool
	 */
	public static function isAssignedToLibrary(ilObjUser $ilObjUser) {
		$activeRecordList = xdglLibrarian::where(array( 'usr_id' => $ilObjUser->getId() ));
		if ($activeRecordList->hasSets()) {
			return $activeRecordList->first()->getLibraryId();
		}

		return false;
	}


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
		if ($this->getIsPrimary()) {
			$this->not_deletable_reason = 1;

			return false;
		}
		if ($this->getRequestCount() > 0) {
			$this->not_deletable_reason = 2;

			return false;
		}

		if ($this->getLibrarianCount() > 0) {
			$this->not_deletable_reason = 3;

			return false;
		}

		return true;
	}


	public function afterObjectLoad() {
		$this->setLibrarians(xdglLibrarian::where(array( 'library_id' => $this->getId() ))->get());
	}


	/**
	 * @return bool
	 */
	public function delete() {
		if ($this->getIsPrimary()) {
			return false;
		}
		if (self::where(array( 'is_primary' => 1 ))->count() == 0) {
			$this->makePrimary();

			return false;
		}
		parent::delete();
	}


	public function makePrimary() {
		global $ilDB;
		/**
		 * @var $ilDB ilDB
		 */
		$ilDB->manipulate('UPDATE ' . $this->getConnectorContainerName() . ' SET is_primary = 0');
		$this->setIsPrimary(true);
		$this->update();
	}


	/**
	 * @return bool
	 */
	public function getRequestCount() {
		static $count = NULL;
		if ($count === NULL) {
			$count = xdglRequest::where(array( 'library_id' => $this->getId() ))->count();
		}

		return $count;
	}


	/**
	 * @return bool
	 */
	public function getLibrarianCount() {
		return count($this->getLibrarians());
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
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getExtId() {
		return $this->ext_id;
	}


	/**
	 * @param string $ext_id
	 */
	public function setExtId($ext_id) {
		$this->ext_id = $ext_id;
	}


	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}


	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}


	/**
	 * @return int
	 */
	public function getIsPrimary() {
		return $this->is_primary;
	}


	/**
	 * @param int $is_primary
	 */
	public function setIsPrimary($is_primary) {
		$this->is_primary = $is_primary;
	}


	/**
	 * @return int
	 */
	public function getAssignedRequestsCount() {
		return $this->assigned_requests_count;
	}


	/**
	 * @param int $assigned_requests_count
	 */
	public function setAssignedRequestsCount($assigned_requests_count) {
		$this->assigned_requests_count = $assigned_requests_count;
	}


	/**
	 * @return xdglLibrarian[]
	 */
	public function getLibrarians() {
		return $this->librarians;
	}


	/**
	 * @param xdglLibrarian[] $librarians
	 */
	public function setLibrarians($librarians) {
		$this->librarians = $librarians;
	}


	/**
	 * @return int
	 */
	public function getNotDeletableReason() {
		return $this->not_deletable_reason;
	}


	/**
	 * @param int $not_deletable_reason
	 */
	public function setNotDeletableReason($not_deletable_reason) {
		$this->not_deletable_reason = $not_deletable_reason;
	}
}

?>
