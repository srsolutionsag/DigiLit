<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ilDigiLitConfigGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 */
class ilDigiLitConfigGUI extends ilPluginConfigGUI {

	/**
	 * TODO: Refactor
	 */
	public function executeCommand() {
		global $ilCtrl, $ilTabs, $lng, $tpl;
		/**
		 * @var ilCtrl $ilCtrl
		 */

		$ilCtrl->redirectByClass(array( ilUIPluginRouterGUI::class, xdglMainGUI::class ));
		$ilCtrl->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $_GET["ctype"]);
		$ilCtrl->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $_GET["cname"]);
		$ilCtrl->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $_GET["slot_id"]);
		$ilCtrl->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $_GET["plugin_id"]);
		$ilCtrl->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $_GET["pname"]);

		$tpl->setTitle($lng->txt("cmps_plugin") . ": " . $_GET["pname"]);
		$tpl->setDescription("");

		$ilTabs->clearTargets();

		if ($_GET["plugin_id"]) {
			$ilTabs->setBackTarget($lng->txt("cmps_plugin"), $ilCtrl->getLinkTargetByClass(ilObjComponentSettingsGUI::class, "showPlugin"));
		} else {
			$ilTabs->setBackTarget($lng->txt("cmps_plugins"), $ilCtrl->getLinkTargetByClass(ilObjComponentSettingsGUI::class, "listPlugins"));
		}

		$a_gui_object = new xdglMainGUI();
		$a_gui_object->executeCommand();
		//		$ilCtrl->forwardCommand($a_gui_object);
	}


	public function performCommand($cmd) {
	}
}
