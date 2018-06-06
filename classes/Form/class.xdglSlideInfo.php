<?php

/**
 * Class xdglSlideInfo
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglSlideInfo {

	/**
	 * @var string
	 */
	protected $body = '';


	public function __construct() {
		$this->pl = ilDigiLitPlugin::getInstance();
		$this->tpl = $this->pl->getTemplate('default/tpl.eula.html');
	}


	public function getHTML() {
		$this->tpl->setVariable('TXT_SHOW', $this->pl->txt('request_eula_show'));
		$this->tpl->setVariable('EULA', xdglConfig::getConfigValue(xdglConfig::F_EULA_TEXT));
	}


	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}


	/**
	 * @param string $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}
}


