<?php

require_once __DIR__ . '/../vendor/autoload.php';

use srag\DIC\DigiLit\DICTrait;

/**
 * Class xdglMainGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy xdglMainGUI : ilUIPluginRouterGUI, ilUIPluginRouterGUI
 * @ilCtrl_IsCalledBy xdglMainGUI : ilDigiLitConfigGUI
 */
class xdglMainGUI
{
    const TAB_SETTINGS = 'settings';
    const TAB_LIBRARIES = 'libraries';
    const TAB_REQUESTS = 'requests';
    /**
     * @var ilTabsGUI
     */
    protected $tabs;
    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilGlobalTemplateInterface
     */
    protected $tpl;
    /**
     * @var ilDigiLitPlugin
     */
    protected $pl;
    /**
     * @var ilObjUser
     */
    protected $user;

    public function __construct()
    {
        global $DIC;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->user = $DIC->user();
        $this->tabs = $DIC->tabs();
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilDigiLitPlugin::getInstance();
    }

    private function initTabs()
    {
        $this->tabs->addTab(
            self::TAB_REQUESTS,
            $this->pl->txt('tab_' . self::TAB_REQUESTS),
            $this->ctrl->getLinkTargetByClass(xdglRequestGUI::class)
        );
        if (ilObjDigiLitAccess::isAdmin()) {
            $this->tabs->addTab(
                self::TAB_SETTINGS,
                $this->pl->txt('tab_' . self::TAB_SETTINGS),
                $this->ctrl->getLinkTargetByClass(xdglConfigGUI::class)
            );
            if (xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES)) {
                $this->tabs->addTab(
                    self::TAB_LIBRARIES,
                    $this->pl->txt('tab_' . self::TAB_LIBRARIES),
                    $this->ctrl->getLinkTargetByClass(xdglLibraryGUI::class)
                );
            }
        }
    }

    /**
     *
     */
    public function executeCommand()
    {
        ilObjDigiLitAccess::hasAccessToMainGUI(true);
        $this->initTabs();
        if (
            !ilObjDigiLitAccess::isAdmin()
            && xdglConfig::getConfigValue(xdglConfig::F_USE_LIBRARIES)
            && xdglConfig::getConfigValue(xdglConfig::F_OWN_LIBRARY_ONLY)
            && !xdglLibrary::isAssignedToAnyLibrary($this->user)) {
            ilUtil::sendInfo($this->pl->txt('no_library_assigned'), true);
            ilUtil::redirect('/');
        }

        $next_class = $this->ctrl->getNextClass();
        if (!xdglConfig::isConfigUpToDate()) {
            ilUtil::sendInfo($this->pl->txt("conf_out_of_date"));
            $next_class = strtolower(xdglConfigGUI::class);
        }

        switch ($next_class) {
            case strtolower(xdglConfigGUI::class);
                $this->tabs->activateTab(self::TAB_SETTINGS);
                $this->ctrl->forwardCommand(new xdglConfigGUI());

                break;
            case strtolower(xdglLibraryGUI::class);
                $this->tabs->activateTab(self::TAB_LIBRARIES);
                $this->ctrl->forwardCommand(new xdglLibraryGUI());
                break;
            default:
                $this->tabs->activateTab(self::TAB_REQUESTS);
                $this->ctrl->forwardCommand(new xdglRequestGUI());

                break;
        }
        $this->tpl->loadStandardTemplate();
        $this->tpl->printToStdout();

    }
}
