<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

/**
 * Access/Condition checking for DigiLit object
 *
 * Please do not create instances of large application classes (like ilObjDigiLit)
 * Write small methods within this class to determine the status.
 *
 * @author        Fabian Schmid <fs@studer-raimann.ch>
 * @author        Martin Studer <ms@studer-raimann.ch>
 * @author        Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version       1.0.00
 */
class ilObjDigiLitAccess extends ilObjectPluginAccess {

	const TXT_PERMISSION_DENIED = 'permission_denied';
	/**
	 * @var array
	 */
	protected static $cache = array();


	/**
	 * @param string $a_cmd
	 * @param string $a_permission
	 * @param int    $a_ref_id
	 * @param int    $a_obj_id
	 * @param string $a_user_id
	 *
	 * @return bool
	 */
	function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = '') {
		global $ilUser, $ilAccess;
		/**
		 * @var $ilAccess ilAccessHandler
		 */
		if ($a_user_id == '') {
			$a_user_id = $ilUser->getId();
		}
		switch ($a_permission) {
			case 'read':
				if (!ilObjDigiLitAccess::checkOnline($a_obj_id) AND !$ilAccess->checkAccessOfUser($a_user_id, 'write', '', $a_ref_id)) {
					return true;
				}
				break;
		}

		return true;
	}


	protected static function redirectNonAccess() {
		global $ilCtrl;
		ilUtil::sendFailure(ilDigiLitPlugin::getInstance()->txt(self::TXT_PERMISSION_DENIED), true);
		$ilCtrl->redirectByClass(ilRepositoryGUI::class);
	}


	/**
	 * @param $digi_lit_ref_id
	 *
	 * @return bool
	 */
	public static function hasAccessToDownload($digi_lit_ref_id) {
		global $ilUser;

		$crs_ref_id = ilObjDigiLit::returnParentCrsRefId($digi_lit_ref_id);
		$p = ilCourseParticipants::getInstanceByObjId(ilObject2::_lookupObjectId($crs_ref_id));
		$a_usr_id = $ilUser->getId();

		$is_member = ($p->isAdmin($a_usr_id) OR $p->isTutor($a_usr_id) OR $p->isMember($a_usr_id));

		$checkAccess = (new self)->_checkAccess('read', 'read', $digi_lit_ref_id, ilObject2::_lookupObjectId($digi_lit_ref_id));

		$not_anonymous = $ilUser->getId() != ANONYMOUS_USER_ID;

		return $is_member AND $checkAccess AND $not_anonymous;
	}


	/**
	 * @param bool $redirect
	 *
	 * @return bool
	 */
	public static function isGlobalAdmin($redirect = false) {
		global $rbacreview, $ilUser;
		/**
		 * @var $rbacreview ilRbacReview
		 */

		$isAssigned = $rbacreview->isAssigned($ilUser->getId(), 2);

		if (!$isAssigned AND $redirect) {
			self::redirectNonAccess();
		}

		return $isAssigned;
	}


	/**
	 * @return bool
	 */
	public static function showAllLibraries() {
		if (!xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES)) {
			return true;
		}
		if (self::isAdmin()) {
			return true;
		}

		if (self::isGlobalAdmin()) {
			return true;
		}

		if (xdglConfig::getConfigValue(xdglConfig::F_OWN_LIBRARY_ONLY)) {
			return false;
		}

		return true;
	}


	/**
	 * @param bool $redirect
	 *
	 * @return bool
	 */
	public static function isAdmin($redirect = false) {
		global $rbacreview, $ilUser;
		$configValue = xdglConfig::getConfigValue(xdglConfig::F_ROLES_ADMIN);
		$configValue = is_array($configValue) ? $configValue : array();
		foreach ($configValue as $role_id) {
			if ($rbacreview->isAssigned($ilUser->getId(), $role_id)) {
				return true;
			}
		}
		if (self::isGlobalAdmin()) {
			return true;
		}
		if ($redirect) {
			self::redirectNonAccess();
		}

		return false;
	}


	/**
	 * @param bool $redirect
	 *
	 * @return bool
	 */
	public static function isManager($redirect = false) {
		global $rbacreview, $ilUser;
		if (ilDigiLitPlugin::getInstance()->isActive()) {
			foreach (xdglConfig::getConfigValue(xdglConfig::F_ROLES_MANAGER) as $role_id) {
				if ($rbacreview->isAssigned($ilUser->getId(), $role_id)) {
					return true;
				}
			}
			if (self::isAdmin()) {
				return true;
			}

			if (self::isGlobalAdmin()) {
				return true;
			}
		}

		if ($redirect) {
			self::redirectNonAccess();
		}

		return false;
	}


	/**
	 * @param $a_id
	 *
	 * @return bool
	 */
	static function checkOnline($a_id) {
		return true;
	}
}
