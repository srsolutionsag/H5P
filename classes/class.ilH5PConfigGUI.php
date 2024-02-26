<?php

declare(strict_types=1);

/**
 * This class is the entry-point of all 'administrative' controllers.
 *
 * When implementing a new controller which should be reachable by any
 * other administrative controller, this class must be extended with an
 * according \@ilCtrl_Calls statement. If the controller should also be
 * reachable from the repository, do the same in @see ilObjH5PGUI
 *
 * @ilCtrl_isCalledBy ilH5PConfigGUI: ilObjComponentSettingsGUI
 *
 * @ilCtrl_Calls      ilH5PConfigGUI: ilH5PGeneralSettingsGUI
 * @ilCtrl_Calls      ilH5PConfigGUI: ilH5PLibraryContentsGUI
 * @ilCtrl_Calls      ilH5PConfigGUI: ilH5PLibraryGUI
 *
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
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
            case strtolower(ilH5PLibraryContentsGUI::class):
                $DIC->ctrl()->forwardCommand(new ilH5PLibraryContentsGUI());
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
