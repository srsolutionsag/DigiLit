<?php

/**
 * Class xdglConfig
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 */
class xdglConfig extends ActiveRecord
{

    const TABLE_NAME = 'xdgl_config';
    const CONFIG_VERSION = 2;
    const F_ROLES_ADMIN = 'permission';
    const F_ROLES_MANAGER = 'permission_manager';
    const F_MAIL_NEW_REQUEST = 'mail_new_request';
    const F_MAIL_REJECTED = 'mail_rejected';
    const F_MAIL_UPLOADED = 'mail_uploaded';
    const F_MAIL_MOVED = 'mail_moved';
    const F_MAIL = 'mail';
    const F_CONFIG_VERSION = 'config_version';
    const F_MAX_DIGILITS = 'max_digilits';
    const F_EULA_TEXT = 'eula_text';
    const F_USE_LIBRARIES = 'use_libraries';
    const F_USE_SEARCH = 'use_search';
    const F_OWN_LIBRARY_ONLY = 'own_library_only';
    const F_USE_REGEX = 'use_regex';
    const F_MAX_REQ_TEXT = 'max_requests_text';
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
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::TABLE_NAME;
    }

    /**
     * @return string
     * @deprecated
     */
    public static function returnDbTableName()
    {
        return self::TABLE_NAME;
    }

    /**
     * @return bool
     */
    public static function isConfigUpToDate()
    {
        return self::getConfigValue(self::F_CONFIG_VERSION) == self::CONFIG_VERSION;
    }

    /**
     * @return bool
     */
    public static function hasValidRegex()
    {
        if (!self::getConfigValue(self::F_USE_REGEX)) {
            return false;
        }

        return self::isRegexValid(self::getConfigValue(self::F_REGEX));
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function getConfigValue($name)
    {
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
     * @param string $name
     * @param mixed  $value
     */
    public static function setConfigValue($name, $value)
    {
        $obj = new self($name);
        $obj->setValue(json_encode($value));

        if (self::where(array('name' => $name))->hasSets()) {
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
     * @param string $regex
     *
     * @return bool
     */
    public static function isRegexValid($regex)
    {
        if (preg_match("/^\/.+\/[a-z]*$/i", $regex)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
