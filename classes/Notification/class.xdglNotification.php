<?php

/**
 * Class xdglNotification
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 *
 */
class xdglNotification extends ilMailNotification {

	const TYPE_NEW_REQUEST = xdglConfig::F_MAIL_NEW_REQUEST;
	const TYPE_REJECTED = xdglConfig::F_MAIL_REJECTED;
	const TYPE_ULOADED = xdglConfig::F_MAIL_UPLOADED;
	const TYPE_MOVED = xdglConfig::F_MAIL_MOVED;
	const R_TITLE = 'REQUEST_TITLE';
	const R_AUTHOR = 'REQUEST_AUTHOR';
	const R_REQUESTER = 'REQUESTER';
	const ADMIN_LINK = 'ADMIN_LINK';
	const R_REQUESTER_FULLNAME = 'REQUESTER_FULLNAME';
	const R_REASON = 'REASON';
	const R_COURSE_NUMBER = 'COURSE_NUMBER';
	const R_BOOK = 'BOOK';
	const R_EDITOR = 'EDITOR';
	const R_LOCATION = 'LOCATION';
	const R_PUBLISHER = 'PUBLISHER';
	const R_PUBLISHING_YEAR = 'PUBLISHING_YEAR';
	const R_PAGES = 'PAGES';
	const R_VOLUME = 'VOLUME';
	const R_EDITION_RELEVANT = 'EDITION_RELEVANT';
	const R_ISSN = 'ISSN';
	const R_LAST_MODIFIED_BY_USER = 'LAST_MODIFIED_BY_USER';
	const R_ASSIGNED_LIBRARY = 'ASSIGNED_LIBRARY';
	const R_ASSIGNED_LIBRARIAN = 'ASSIGNED_LIBRARIAN';
	const R_NOTICE = 'NOTICE';
	const R_ALL = 'ALL';
	const R_INTERNAL_NOTICE = 'INTERNAL_NOTICE';
	/**
	 * @var array
	 */
	protected static $placeholders = array(
		self::TYPE_NEW_REQUEST => array(
			xdglNotification::R_TITLE,
			xdglNotification::R_AUTHOR,
			xdglNotification::R_REQUESTER_FULLNAME,
			xdglNotification::R_COURSE_NUMBER,
			xdglNotification::R_BOOK,
			xdglNotification::R_EDITOR,
			xdglNotification::R_LOCATION,
			xdglNotification::R_PUBLISHER,
			xdglNotification::R_PUBLISHING_YEAR,
			xdglNotification::R_PAGES,
			xdglNotification::R_VOLUME,
			xdglNotification::R_EDITION_RELEVANT,
			xdglNotification::R_ISSN,
			xdglNotification::R_LAST_MODIFIED_BY_USER,
			xdglNotification::R_ASSIGNED_LIBRARY,
			xdglNotification::R_ASSIGNED_LIBRARIAN,
			xdglNotification::R_NOTICE,
			xdglNotification::R_ALL,
		),
		self::TYPE_ULOADED => array(
			xdglNotification::R_TITLE,
			xdglNotification::R_AUTHOR,
			xdglNotification::R_COURSE_NUMBER,
			xdglNotification::R_BOOK,
			xdglNotification::R_EDITOR,
			xdglNotification::R_LOCATION,
			xdglNotification::R_PUBLISHER,
			xdglNotification::R_PUBLISHING_YEAR,
			xdglNotification::R_PAGES,
			xdglNotification::R_VOLUME,
			xdglNotification::R_EDITION_RELEVANT,
			xdglNotification::R_ISSN,
			xdglNotification::R_ASSIGNED_LIBRARY,
			xdglNotification::R_ASSIGNED_LIBRARIAN,
			xdglNotification::R_NOTICE,
			xdglNotification::R_ALL,
		),
		self::TYPE_REJECTED => array(
			xdglNotification::R_TITLE,
			xdglNotification::R_AUTHOR,
			xdglNotification::R_REASON,
			xdglNotification::R_COURSE_NUMBER,
			xdglNotification::R_BOOK,
			xdglNotification::R_EDITOR,
			xdglNotification::R_LOCATION,
			xdglNotification::R_PUBLISHER,
			xdglNotification::R_PUBLISHING_YEAR,
			xdglNotification::R_PAGES,
			xdglNotification::R_VOLUME,
			xdglNotification::R_EDITION_RELEVANT,
			xdglNotification::R_ISSN,
			xdglNotification::R_ASSIGNED_LIBRARY,
			xdglNotification::R_ASSIGNED_LIBRARIAN,
			xdglNotification::R_NOTICE,
			xdglNotification::R_ALL,
		),
		self::TYPE_MOVED => array(
			xdglNotification::R_TITLE,
			xdglNotification::R_AUTHOR,
			xdglNotification::R_REQUESTER_FULLNAME,
			xdglNotification::R_COURSE_NUMBER,
			xdglNotification::R_BOOK,
			xdglNotification::R_EDITOR,
			xdglNotification::R_LOCATION,
			xdglNotification::R_PUBLISHER,
			xdglNotification::R_PUBLISHING_YEAR,
			xdglNotification::R_PAGES,
			xdglNotification::R_VOLUME,
			xdglNotification::R_EDITION_RELEVANT,
			xdglNotification::R_ISSN,
			xdglNotification::R_LAST_MODIFIED_BY_USER,
			xdglNotification::R_ASSIGNED_LIBRARY,
			xdglNotification::R_ASSIGNED_LIBRARIAN,
			//			xdglNotification::R_INTERNAL_NOTICE,
			xdglNotification::R_NOTICE,
			xdglNotification::R_ALL,
		),
	);


	/**
	 * @param int $type
	 *
	 * @return array
	 */
	public static function getPlaceHoldersForType($type) {
		return self::$placeholders[$type];
	}


	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var xdglRequest
	 */
	protected $xdglRequest;
	/**
	 * @var ilObjUser
	 */
	protected $ilObjUser;


	/**
	 * @param xdglRequest $xdglRequest
	 *
	 * @return bool
	 */
	public static function sendNew(xdglRequest $xdglRequest) {
		$obj = new self();
		$obj->setType(self::TYPE_NEW_REQUEST);
		$obj->setXdglRequest($xdglRequest);

		return $obj->send();
	}


	/**
	 * @param xdglRequest $xdglRequest
	 *
	 * @return bool
	 */
	public static function sendUploaded(xdglRequest $xdglRequest) {
		$obj = new self();
		$obj->setType(self::TYPE_ULOADED);
		$obj->setXdglRequest($xdglRequest);

		return $obj->send();
	}


	/**
	 * @param xdglRequest $xdglRequest
	 *
	 * @return bool
	 */
	public static function sendRejected(xdglRequest $xdglRequest) {
		$obj = new self();
		$obj->setType(self::TYPE_REJECTED);
		$obj->setXdglRequest($xdglRequest);

		return $obj->send();
	}


	/**
	 * @param xdglRequest $xdglRequest
	 *
	 * @return bool
	 */
	public static function sendMoved(xdglRequest $xdglRequest) {
		$obj = new self();
		$obj->setType(self::TYPE_MOVED);
		$obj->setXdglRequest($xdglRequest);

		return $obj->send();
	}


	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function getReplace($field) {
		global $ilCtrl;
		/**
		 * @var ilCtrl $ilCtrl
		 */
		$this->initUser();

		switch ($field) {
			case self::R_ALL:
				$return = '';
				foreach (self::$placeholders[$this->getType()] as $v) {
					if ($v != self::R_ALL) {
						$return .= $v . ': ' . self::getReplace($v) . "\n";
					}
				}

				return $return;

			case self::ADMIN_LINK:
				$ilCtrl->setParameterByClass(xdglRequestGUI::class, xdglRequestGUI::XDGL_ID, $this->getXdglRequest()->getId());

			//				return urldecode(ilUtil::_getHttpPath() . '/' . $ilCtrl->getLinkTargetByClass(array(
			//						ilUIPluginRouterGUI::class,
			//						xdglRequestGUI::class
			//					), xdglRequestGUI::CMD_VIEW, '', false, false));
			case self::R_TITLE:
				return $this->getXdglRequest()->getTitle();
			case self::R_AUTHOR:
				return $this->getXdglRequest()->getAuthor();
			case self::R_REQUESTER:
				return $this->getXdglRequest()->getRequesterUsrId();
			case self::R_REQUESTER_FULLNAME:
				return $this->ilObjUser->getFullname();
			case self::R_REASON:
				return $this->getXdglRequest()->getRejectionReason();
			case self::R_COURSE_NUMBER:
				return $this->getXdglRequest()->getCourseNumber();
			case self::R_BOOK:
				return $this->getXdglRequest()->getBook();
			case self::R_EDITOR:
				return $this->getXdglRequest()->getEditor();
			case self::R_LOCATION:
				return $this->getXdglRequest()->getLocation();
			case self::R_PUBLISHING_YEAR:
				return $this->getXdglRequest()->getPublishingYear();
			case self::R_PUBLISHER:
				return $this->getXdglRequest()->getPublisher();
			case self::R_PAGES:
				return $this->getXdglRequest()->getPages();
			case self::R_VOLUME:
				return $this->getXdglRequest()->getVolume();
			case self::R_INTERNAL_NOTICE:
				return $this->getXdglRequest()->getInternalNotice();
			case self::R_NOTICE:
				return $this->getXdglRequest()->getNotice();
			case self::R_EDITION_RELEVANT:
				return $this->getXdglRequest()->getEditionRelevant() ? 'YES' : 'NO';
			case self::R_ISSN:
				return $this->getXdglRequest()->getIssn();
			case self::R_LAST_MODIFIED_BY_USER:
				$usr_id = $this->getXdglRequest()->getLastModifiedByUsrId();
				$obj = new ilObjUser($usr_id);

				return $obj->getFullname();
				break;
			case self::R_ASSIGNED_LIBRARY:
				$lib_id = $this->getXdglRequest()->getLibraryId();
				/**
				 * @var xdglLibrary $xdglLibrary
				 */
				$xdglLibrary = xdglLibrary::find($lib_id);
				if ($xdglLibrary instanceof xdglLibrary) {
					return $xdglLibrary->getTitle();
				}
			case self::R_ASSIGNED_LIBRARIAN:
				$lib_id = $this->getXdglRequest()->getLibrarianId();
				if (!$lib_id) {
					return 'NOBODY';
				}
				/**
				 * @var xdglLibrarian $xdglLibrary
				 */

				$activeRecordList = xdglLibrarian::where(array( 'usr_id' => $lib_id ));
				if ($activeRecordList->hasSets()) {
					$xdglLibrarian = $activeRecordList->first();
					if ($xdglLibrarian instanceof xdglLibrarian) {
						$usr_id = $xdglLibrarian->getUsrId();
						$obj = new ilObjUser($usr_id);

						return $obj->getFullname() . ' (' . $obj->getEmail() . ')';
					}
				}

				return "";
		}

		return '';
	}


	/**
	 * @return string
	 */
	public function getAdress() {
		switch ($this->getType()) {
			case self::TYPE_MOVED:
			case self::TYPE_NEW_REQUEST:
				$lib_id = $this->getXdglRequest()->getLibraryId();
				/**
				 * @var xdglLibrary $xdglLibrary
				 */
				$xdglLibrary = xdglLibrary::find($lib_id);
				if ($xdglLibrary instanceof xdglLibrary) {
					return $xdglLibrary->getEmail();
				} else {
					return xdglConfig::getConfigValue(xdglConfig::F_MAIL);
				}

				break;
			case self::TYPE_ULOADED:
			case self::TYPE_REJECTED:
				return $this->ilObjUser->getEmail();
				break;
		}
	}


	/**
	 * @return string
	 */
	public function replaceBody() {
		$placeholders = self::getPlaceHoldersForType($this->getType());
		$body = xdglConfig::getConfigValue($this->getType());
		foreach ($placeholders as $k) {
			$body = str_replace('[' . $k . ']', $this->getReplace($k), $body);
		}
		$this->setBody($body);
		$this->getMail()->appendInstallationSignature(true);
	}


	/**
	 * Send notifications
	 *
	 * @return bool
	 */
	public function send() {
		global $ilUser;
		/**
		 * @var ilObjUser $ilUser
		 */
		$this->setSender($ilUser->getId());
		$this->initUser();
		$this->initLanguage($this->ilObjUser->getId());
		$this->initMail();
		$a_subject = $this->getXdglRequest()->getExtId() . ': ' . ilDigiLitPlugin::getInstance()->txt('notification_subject_' . $this->getType());
		$this->setSubject($a_subject);
		$this->replaceBody();

		$this->sendMail(array( $this->getAdress() ), array( 'normal' ), false);
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return xdglRequest
	 */
	public function getXdglRequest() {
		return $this->xdglRequest;
	}


	/**
	 * @param xdglRequest $xdglRequest
	 */
	public function setXdglRequest($xdglRequest) {
		$this->xdglRequest = $xdglRequest;
	}


	protected function initUser() {
		if (!isset($this->ilObjUser)) {
			$this->ilObjUser = new ilObjUser($this->getXdglRequest()->getRequesterUsrId());
		}
	}
}
