<?php

/**
 * xdglRequest
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version 1.0.00
 */
class xdglRequest extends ActiveRecord {

	const TABLE_NAME = 'xdgl_request';
	const STATUS_DELETED = -1;
	const STATUS_NEW = 1;
	const STATUS_IN_PROGRRESS = 2;
	const STATUS_RELEASED = 3;
	const STATUS_REFUSED = 4;
	const STATUS_COPY = 5;
	const STATUS_ASSIGNED = 6;
	const LIBRARIAN_ID_NONE = 1;
	const LIBRARIAN_ID_MINE = 2;
	const EXT_ID_PREFIX = '';
	const UNKNOWN_PREFIX = 'UNKNOWN-';
	/**
	 * @var array
	 */
	protected static $countable_status = array(
		self::STATUS_NEW,
		self::STATUS_IN_PROGRRESS,
		self::STATUS_RELEASED,
	);

	/**
	 * @var array
	 * request_status_1#:#Neu
	 * request_status_2#:#In Bearbeitung
	* request_status_3#:#Dokument vorhanden
	* request_status_4#:#Antrag abgelehnt
	* request_status_5#:#Kopiert
	 */
	protected static $status_to_string_map = array(
		self::STATUS_NEW => 'request_status_1',
		self::STATUS_IN_PROGRRESS => 'request_status_2',
		self::STATUS_RELEASED => 'request_status_3',
		self::STATUS_REFUSED => 'request_status_4',
		self::STATUS_ASSIGNED => 'request_status_6',
	);


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @db_has_field          true
	 * @db_fieldtype          integer
	 * @db_length             4
	 * @db_sequence           true
	 * @db_is_primary         true
	 */
	protected $id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 * @db_is_notnull       true
	 */
	protected $status = self::STATUS_NEW;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 * @db_is_notnull       true
	 */
	protected $course_number;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 * @db_is_notnull       true
	 */
	protected $author;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 * @db_is_notnull       true
	 */
	protected $title;
	/**
	 * @var string (declaring whether the literal work is a book or a journal)
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 * @db_is_notnull       true
	 */
	protected $book;
	/**
	 * @var string (german: Herausgeber)
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $editor;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $location;
	/**
	 * @var string (german: Verlag)
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $publisher;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           2
	 * @db_is_notnull       true
	 */
	protected $publishing_year;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $pages;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $volume;
	/**
	 * @var int (used as boolean)
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 * @db_is_notnull       true
	 */
	protected $edition_relevant = 0;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $issn;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $last_change;
	/**
	 * @var String saves a timestamp, when the request date changed the last time
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $date_last_status_change;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           4
	 */
	protected $digi_lit_object_id;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           4
	 */
	protected $version = 1;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $crs_ref_id;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $requester_usr_id = 6;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $last_modified_by_usr_id = 6;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $rejection_reason = '';
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $copy_id = null;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $library_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $librarian_id = 0;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           2000
	 */
	protected $notice = '';
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           2000
	 */
	protected $internal_notice = '';
	/**
	 * @var string
	 */
	protected $ext_id = '';
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           4
	 */
	protected $number_of_usages;



	public function afterObjectLoad() {
		if (xdglConfig::getConfigValue(xdglConfig::F_USE_REGEX)) {
			preg_match(xdglConfig::getConfigValue(xdglConfig::F_REGEX), $this->getCourseNumber(), $matches);
			$form_id = sprintf('%05d', $this->getId());
			if ($matches[1]) {
				$this->setExtId(self::EXT_ID_PREFIX . $matches[1] . '-' . $form_id);
			} else {
				$this->setExtId(self::EXT_ID_PREFIX . self::UNKNOWN_PREFIX . $form_id);
			}
		} else {
			$this->setExtId('DGL-' . $this->getId());
		}
	}


	/**
	 * @param bool $prevent_last_change
	 * @param bool $update_title
	 */
	public function update($prevent_last_change = false, $update_title = true) {
		if ($this->getLibrarianId() === null) {
			$this->setLibrarianId(self::LIBRARIAN_ID_NONE);
		}
		if (!$prevent_last_change) {
			global $ilUser;
			$this->setLastChange(time());
			$this->setLastModifiedByUsrId($ilUser->getId());
		}
		parent::update();
		if ($update_title) {
			$this->updateIliasObjTitle();
		}
	}


	public function create() {
		global $ilUser;
		if ($this->getLibrarianId() === null) {
			$this->setLibrarianId(self::LIBRARIAN_ID_NONE);
		}
		$this->setRequesterUsrId($ilUser->getId());
		$this->setCreateDate(time());
		$this->setLastChange(time());
		$this->setDateLastStatusChange(time());
		$this->setLibraryId(xdglLibrary::getPrimaryId());
		parent::create();
		$this->updateIliasObjTitle();
	}


	/**
	 * @param xdglLibrarian $xdglLibrarian
	 */
	public function assignToLibrarian(xdglLibrarian $xdglLibrarian) {
		$this->setLibrarianId($xdglLibrarian->getUsrId());
		$this->setLibraryId($xdglLibrarian->getLibraryId());
		$this->setStatus(self::STATUS_NEW);
		$this->update();
		xdglNotification::sendMoved($this);
	}


	/**
	 * @param xdglLibrary $xdglLibrary
	 */
	public function assignToLibrary(xdglLibrary $xdglLibrary) {
		$this->setLibraryId($xdglLibrary->getId());
		$this->setLibrarianId(self::LIBRARIAN_ID_NONE);
		$this->setStatus(self::STATUS_NEW);
		$this->update();
		xdglNotification::sendMoved($this);
	}


	/**
	 * @param       $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return \xdglRequest
	 */
	public static function find($primary_key, array $add_constructor_args = array()) {
		return parent::find($primary_key, $add_constructor_args);
	}


	/**
	 * @return int
	 */
	public function getAmoutOfDigiLitsInCourse() {
		$count = 0;
		if ($this->getCrsRefId()) {
			$count = $this->getAmoutOfDigiLitsInContainer($count, $this->getCrsRefId());
		}

		return $count;
	}


	/**
	 * @return bool
	 */
	public function doesCount() {
		return in_array($this->getStatus(), self::$countable_status);
	}


	/**
	 * @param $count
	 * @param $ref_id
	 *
	 * @return mixed
	 */
	protected static function getAmoutOfDigiLitsInContainer($count, $ref_id) {
		global $tree;
		/**
		 * @var $tree ilTree
		 */

		foreach ($tree->getChildsByType($ref_id, ilDigiLitPlugin::XDGL) as $dig) {
			$ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
			$request_usage = $ilObjDigiLitFacadeFactory->requestUsageFactory()->getInstanceByObjectId($dig['obj_id']);
			if (xdglRequest::find($request_usage->getRequestId())->doesCount()) {
				$count++;
			}
		}

		foreach ($tree->getChildsByTypeFilter($ref_id, array('fold', 'grp')) as $sub) {
			$count = self::getAmoutOfDigiLitsInContainer($count, $sub['ref_id']);
		}

		return $count;
	}


	/**
	 * @return string
	 */
	public function getCourseTitle() {
		if ($this->getCrsRefId()) {
			return ilObject2::_lookupTitle(ilObject2::_lookupObjId($this->getCrsRefId()));
		}

		return '';
	}


	/**
	 * @return string
	 */
	public function getAbsoluteFilePath() {
		return $this->getFilePath() . DIRECTORY_SEPARATOR . $this->returnFileName();
	}


	/**
	 * @return string
	 */
	public function getFilePath() {
		$xdgl = ilDigiLitPlugin::getStaticPluginPrefix();
		$path = ilUtil::getDataDir() . DIRECTORY_SEPARATOR . $xdgl . DIRECTORY_SEPARATOR . self::createPathFromId($this->getId());

		return $path;
	}


	/**
	 * @return bool
	 */
	public function dirExists() {
		return is_dir($this->getFilePath());
	}


	/**
	 * @return bool
	 */
	public function fileExists() {
		$filename = $this->getAbsoluteFilePath();

		return is_file($filename);
	}


	/**
	 * @return bool
	 */
	public function deliverFile() {
		$fileExists = $this->fileExists();
		$status = $this->getStatus();
		if ($fileExists AND $status == self::STATUS_RELEASED) {
			header('Content-Type: application/pdf');

			return ilUtil::deliverFile($this->getAbsoluteFilePath(), $this->getTitle() . '.pdf');
		}

		return false;
	}


	/**
	 * @throws Exception
	 * @return bool
	 */
	public function createDir() {
		if (!$this->dirExists()) {
			if (!ilUtil::makeDirParents($this->getFilePath())) {
				throw new Exception('Unable to create Folder \'' . $this->getFilePath() . '\'. Missing permissions?');
			} else {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param xdglUploadFormGUI $xdglUploadFormGUI
	 *
	 * @return bool
	 */
	public function uploadFileFromForm(xdglUploadFormGUI $xdglUploadFormGUI) {
		$this->createDir();

		if (ilUtil::moveUploadedFile($xdglUploadFormGUI->getUploadTempName(), $xdglUploadFormGUI->getUploadTempName(),
			$this->getAbsoluteFilePath())) {
			global $ilUser;
			$this->setLibrarianId($ilUser->getId());
			$this->setStatus(self::STATUS_RELEASED);
			$this->update();

			return true;
		}
	}


	/**
	 * @return bool
	 * @throws Exception
	 */
	public function deleteFile() {
		if (@unlink($this->getAbsoluteFilePath())) {
			$this->setStatus(self::STATUS_IN_PROGRRESS);
			$this->update();

			return true;
		}
		throw new Exception('An Error occured during file-deletion');
	}



	// ------------------------------------ //
	//       Sleep & Wakeup Function        //
	// ------------------------------------ //
	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return int
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case 'create_date':
			case 'last_change':
			case 'date_last_status_change':
				return strtotime($field_value);
				break;
		}
	}


	/**
	 * @param $field_name
	 *
	 * @return bool|string
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case 'date_last_status_change':
			case 'create_date':
			case 'last_change':
				return date(DATE_ISO8601, $this->{$field_name});
				break;
		}
	}


	/**
	 * Create a path from an id: e.g 12345 will be converted to 1/23/45
	 *
	 * @access public
	 * @static
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public static function createPathFromId($id) {
		$path = array();
		$found = false;
		$id = (int)$id;
		for ($i = 2; $i >= 0; $i--) {
			$factor = pow(100, $i);
			if (($tmp = (int)($id / $factor)) or $found) {
				$path[] = $tmp;
				$id = $id % $factor;
				$found = true;
			}
		}

		$path_string = '';
		if (count($path)) {
			$path_string = implode(DIRECTORY_SEPARATOR, $path);
		}

		return $path_string;
	}


	protected function updateIliasObjTitle() {
		if ($this->getDigiLitObjectId()) {
			/**
			 * @var $ilObjDigiLit ilObjDigiLit
			 */
			$ilObjDigiLit = ilObjectFactory::getInstanceByObjId($this->getDigiLitObjectId());
			$ilObjDigiLit->setTitle($this->getTitle());
			$ilObjDigiLit->update();
		}
	}


	/**
	 * @return string
	 */
	protected function returnFileName() {
		return ilDigiLitPlugin::getStaticPluginName() . '_' . $this->getVersion() . '.pdf';
	}


	/**
	 * @param        $value
	 *
	 * @param string $appendix
	 *
	 * @return string
	 */
	public static function boolTextRepresentation($value, $appendix = '') {
		$value = (int)$value;
		if ($appendix) {
			$appendix = '_' . $appendix;
		}

		return ilDigiLitPlugin::getInstance()->txt('common_bool_' . $value . $appendix);
	}

	// ------------------------------------ //
	//           Static Functions           //
	// ------------------------------------ //

	/**
	 * @param int $digilit_obj_id
	 *
	 * @return int request id
	 */
	public static function getIdByDigiLitObjectId($digilit_obj_id) {
		/**
		 * @var ilDB $ilDB
		 */
		global $ilDB;

		$xdglRequest = new xdglRequest();

		$set = $ilDB->query('SELECT id FROM  ' . $xdglRequest->getConnectorContainerName() . ' WHERE digi_lit_object_id = ' . $ilDB->quote($digilit_obj_id,
				"integer"));
		$row = $ilDB->fetchAssoc($set);

		return $row['id'];
	}


	/**
	 * @param $digilit_obj_id
	 *
	 * @return xdglRequest
	 */
	public static function getInstanceForDigiLitObjectId($digilit_obj_id) {
		return self::find(self::getIdByDigiLitObjectId($digilit_obj_id));
	}


	/**
	 * Set the status of one specific digilit object
	 *
	 * @param int $request_id
	 * @param int $status
	 *
	 * @deprecated
	 */
	public static function setDigilitStatus($request_id, $status) {
		$request = new self($request_id);
		$request->setStatus($status);
		$request->update();
	}


	/**
	 * @param string $search_title
	 * @param string $search_author
	 * @param int    $limit
	 *
	 * @return array
	 */
	public static function findDistinctRequestsByTitleAndAuthor($search_title, $search_author, $limit) {
		global $ilDB;
		$query = "SELECT DISTINCT id, status, author, title, book, publisher, location, publishing_year, pages FROM ilias.xdgl_request where title LIKE ".
			$ilDB->quote("%" . $search_title . "%", "text") . " AND author LIKE ". $ilDB->quote("%" . $search_author . "%", "text") . " AND status != -1 GROUP BY author, title, book LIMIT " . $ilDB->quote($limit, "integer");
		$set = $ilDB->query($query);
		$requests = [];
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$requests[] = $rec;
		}
		$pl = ilDigiLitPlugin::getInstance();
		foreach($requests as $key => $request_data) {
			$requests[$key]['status'] = $pl->txt(self::$status_to_string_map[$request_data['status']]);
		}
		return $requests;
	}



	// ------------------------------------ //
	//          Setters & Getters           //
	// ------------------------------------ //

	/**
	 * @param string $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}


	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->author;
	}


	/**
	 * @param int $course_number
	 */
	public function setCourseNumber($course_number) {
		$this->course_number = $course_number;
	}


	/**
	 * @return int
	 */
	public function getCourseNumber() {
		return $this->course_number;
	}


	/**
	 * @param int $create_date_unix_timestamp
	 */
	public function setCreateDate($create_date_unix_timestamp) {
		$this->create_date = $create_date_unix_timestamp;
	}


	/**
	 * @return int Unix-Timestamp
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param int $edition_relevant
	 */
	public function setEditionRelevant($edition_relevant) {
		$this->edition_relevant = $edition_relevant;
	}


	/**
	 * @return int
	 */
	public function getEditionRelevant() {
		return $this->edition_relevant;
	}


	/**
	 * @param string $editor
	 */
	public function setEditor($editor) {
		$this->editor = $editor;
	}


	/**
	 * @return string
	 */
	public function getEditor() {
		return $this->editor;
	}


	/**
	 * @param string $issn
	 */
	public function setIssn($issn) {
		$this->issn = $issn;
	}


	/**
	 * @return string
	 */
	public function getIssn() {
		return $this->issn;
	}


	/**
	 * @param string $location
	 */
	public function setLocation($location) {
		$this->location = $location;
	}


	/**
	 * @return string
	 */
	public function getLocation() {
		return $this->location;
	}


	/**
	 * @param string $pages
	 */
	public function setPages($pages) {
		$this->pages = $pages;
	}


	/**
	 * @return string
	 */
	public function getPages() {
		return $this->pages;
	}


	/**
	 * @param string $publisher
	 */
	public function setPublisher($publisher) {
		$this->publisher = $publisher;
	}


	/**
	 * @return string
	 */
	public function getPublisher() {
		return $this->publisher;
	}


	/**
	 * @param int $publishing_year
	 */
	public function setPublishingYear($publishing_year) {
		$this->publishing_year = $publishing_year;
	}


	/**
	 * @return int
	 */
	public function getPublishingYear() {
		return $this->publishing_year;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
		$this->setDateLastStatusChange(time());
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
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
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $book
	 */
	public function setBook($book) {
		$this->book = $book;
	}


	/**
	 * @return string
	 */
	public function getBook() {
		return $this->book;
	}


	/**
	 * @param int $volume_year
	 */
	public function setVolume($volume_year) {
		$this->volume = $volume_year;
	}


	/**
	 * @return int
	 */
	public function getVolume() {
		return $this->volume;
	}


	/**
	 * @param String $date_last_status_change_unix_timestamp
	 */
	public function setDateLastStatusChange($date_last_status_change_unix_timestamp) {
		$this->date_last_status_change = $date_last_status_change_unix_timestamp;
	}


	/**
	 * @return String
	 */
	public function getDateLastStatusChange() {
		return $this->date_last_status_change;
	}


	/**
	 * @param int $digi_lit_object_id
	 */
	public function setDigiLitObjectId($digi_lit_object_id) {
		$this->digi_lit_object_id = $digi_lit_object_id;
	}


	/**
	 * @return int
	 */
	public function getDigiLitObjectId() {
		return $this->digi_lit_object_id;
	}


	/**
	 * @param int $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}


	/**
	 * @return int
	 */
	public function getVersion() {
		return $this->version;
	}


	/**
	 * @param string $crs_ref_id
	 */
	public function setCrsRefId($crs_ref_id) {
		$this->crs_ref_id = $crs_ref_id;
	}


	/**
	 * @return string
	 */
	public function getCrsRefId() {
		return $this->crs_ref_id;
	}


	/**
	 * @param string $last_modified_by_usr_id
	 */
	public function setLastModifiedByUsrId($last_modified_by_usr_id) {
		$this->last_modified_by_usr_id = $last_modified_by_usr_id;
	}


	/**
	 * @return string
	 */
	public function getLastModifiedByUsrId() {
		return $this->last_modified_by_usr_id;
	}


	/**
	 * @param string $requester_usr_id
	 */
	public function setRequesterUsrId($requester_usr_id) {
		$this->requester_usr_id = $requester_usr_id;
	}


	/**
	 * @return string
	 */
	public function getRequesterUsrId() {
		return $this->requester_usr_id;
	}


	/**
	 * @param string $rejection_reason
	 */
	public function setRejectionReason($rejection_reason) {
		$this->rejection_reason = $rejection_reason;
	}


	/**
	 * @return string
	 */
	public function getRejectionReason() {
		return $this->rejection_reason;
	}


	/**
	 * @param string $copy_id
	 */
	public function setCopyId($copy_id) {
		$this->copy_id = $copy_id;
	}


	/**
	 * @return string
	 */
	public function getCopyId() {
		return $this->copy_id;
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
	 * @return int
	 */
	public function getLibrarianId() {
		return $this->librarian_id;
	}


	/**
	 * @param int $librarian_id
	 */
	public function setLibrarianId($librarian_id) {
		$this->librarian_id = $librarian_id;
	}


	/**
	 * @return string
	 */
	public function getNotice() {
		return $this->notice;
	}


	/**
	 * @param string $notice
	 */
	public function setNotice($notice) {
		$this->notice = $notice;
	}


	/**
	 * @return string
	 */
	public function getInternalNotice() {
		return $this->internal_notice;
	}


	/**
	 * @param string $internal_notice
	 */
	public function setInternalNotice($internal_notice) {
		$this->internal_notice = $internal_notice;
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
	 * @return int
	 */
	public function getLastChange() {
		return $this->last_change;
	}


	/**
	 * @param int $last_change
	 */
	public function setLastChange($last_change) {
		$this->last_change = $last_change;
	}


	/**
	 * @deprecated
	 */
	protected function updateCrsRefId() {
		if (!$this->getCrsRefId()) {
			$refs = ilObject2::_getAllReferences($this->getDigiLitObjectId());
			$ref_id = (array_shift(array_values($refs)));
			if ($ref_id) {
				$ilObjDigiLit = new ilObjDigiLit($ref_id);
				$this->setCrsRefId(ilObjDigiLit::returnParentCrsRefId($ilObjDigiLit->getRefId()));
				$this->update(true, false);
			}
		}
	}

	/**
	 * @return int
	 */
	public function getNumberOfUsages() {
		return $this->number_of_usages;
	}


	/**
	 * @param int $number_of_usages
	 */
	public function setNumberOfUsages($number_of_usages) {
		$this->number_of_usages = $number_of_usages;
	}
}