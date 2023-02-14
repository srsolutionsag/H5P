<?php

declare(strict_types=1);

/**
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * @ilCtrl_isCalledBy ilH5PConfigGUI: ilObjComponentSettingsGUI
 * @ilCtrl_Calls      ilH5PConfigGUI: ilH5PGeneralSettingsGUI
 * @ilCtrl_Calls      ilH5PConfigGUI: ilH5PLibraryGUI
 *
 * @noinspection      AutoloadingIssuesInspection
 */
class ilH5PConfigGUI extends ilPluginConfigGUI
{
    /**
     * @inheritDoc
     */
    public function performCommand($cmd): void
    {
        global $DIC;

        switch ($DIC->ctrl()->getNextClass()) {
            case strtolower(ilH5PGeneralSettingsGUI::class):
                $DIC->ctrl()->forwardCommand(new ilH5PGeneralSettingsGUI());
                break;
            case strtolower(ilH5PLibraryGUI::class):
                $DIC->ctrl()->forwardCommand(new ilH5PLibraryGUI());
                break;

            default:
                // this is redirect-abuse and should be somehow
                // prevented in the future.
                $DIC->ctrl()->redirectByClass(
                    [ilAdministrationGUI::class, ilObjComponentSettingsGUI::class, self::class, ilH5PLibraryGUI::class],
                    ilH5PLibraryGUI::CMD_LIBRARY_INDEX
                );
        }
    }
}
