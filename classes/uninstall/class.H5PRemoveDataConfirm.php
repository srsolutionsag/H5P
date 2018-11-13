<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\H5P\Utils\H5PTrait;
use srag\RemovePluginDataConfirm\H5P\AbstractRemovePluginDataConfirm;

/**
 * Class H5PRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy H5PRemoveDataConfirm: ilUIPluginRouterGUI
 */
class H5PRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
}
