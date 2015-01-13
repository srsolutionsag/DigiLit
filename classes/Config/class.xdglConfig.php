<?php
require_once('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php');

/**
 * Class xdglConfig
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 */
class xdglConfig extends ActiveRecord {

	const F_ROLES_ADMIN = 'permission';
	const F_ROLES_MANAGER = 'permission_manager';
	const F_MAIL_NEW_REQUEST = 'mail_new_request';
	const F_MAIL_REJECTED = 'mail_rejected';
	const F_MAIL_UPLOADED = 'mail_uploaded';
	const F_MAIL_MOVED = 'mail_moved';
	const F_MAIL = 'mail';
	const F_MAX_DIGILITS = 'max_digilits';
	const F_EULA_TEXT = 'eula_text';
	const F_USE_LIBRARIES = 'use_libraries';
	const F_OWN_LIBRARY_ONLY = 'own_library_only';
	const F_USE_REGEX = 'use_regex';
	const F_REGEX = 'regex';
	/**
	 * @var array
	 */
	protected static $cache = array();
	/**
	 * @var array
	 */
	protected static $cache_loaded = array();
	/**
	 * @var bool
	 */
	protected $ar_safe_read = false;


	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function get($name) {
		if (!self::$cache_loaded[$name]) {
			$obj = new self($name);
			if ($_SERVER['REMOTE_ADDR'] == '212.41.220.231') {
				//				var_dump(json_decode($obj->getValue())); // FSX
			}
			self::$cache[$name] = json_decode($obj->getValue());
			self::$cache_loaded[$name] = true;
		}

		return self::$cache[$name];
	}


	/**
	 * @param $name
	 * @param $value
	 */
	public static function set($name, $value) {
		$obj = new self($name);
		$obj->setValue(json_encode($value));

		if (self::where(array( 'name' => $name ))->hasSets()) {
			$obj->update();
		} else {
			$obj->create();
		}
	}


	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_is_unique        true
	 * @db_is_primary       true
	 * @db_is_notnull       true
	 * @db_fieldtype        text
	 * @db_length           250
	 */
	protected $name;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 */
	protected $value;


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return 'xdgl_config';
	}
}

?>
