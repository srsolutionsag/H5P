Demand if plugin data should be removed on uninstall

### Usage

#### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/removeplugindataconfirm": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

#### Use
First declare your plugin class like follow:
```php
//...
use srag\RemovePluginDataConfirm\H5P\PluginUninstallTrait;
//...
use PluginUninstallTrait;
//...
const PLUGIN_CLASS_NAME = self::class;
const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = XRemoveDataConfirm::class;
//...
/**
 * @inheritdoc
 */
protected function deleteData()/*: void*/ {
    // TODO: Delete your plugin data in this method
}
//...
```
`XRemoveDataConfirm` is the name of your remove data confirm class.
You don't need to use `DICTrait`, it is already in use!

If your plugin is a RepositoryObject use `RepositoryObjectPluginUninstallTrait` instead:
```php
//...
use srag\RemovePluginDataConfirm\H5P\RepositoryObjectPluginUninstallTrait;
//...
use RepositoryObjectPluginUninstallTrait;
//...
```

Remove also the methods `beforeUninstall`, `afterUninstall`, `beforeUninstallCustom` and `uninstallCustom` in your plugin class.

Then create a class called `XRemoveDataConfirm` in `classes/uninstall/class.XRemoveDataConfirm.php`:
```php
<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\RemovePluginDataConfirm\H5P\AbstractRemovePluginDataConfirm;

/**
 * Class XRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy XRemoveDataConfirm: ilUIPluginRouterGUI
 */
class XRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

    const PLUGIN_CLASS_NAME = ilXPlugin::class;
}

```
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).
Replace the `X` in `XRemoveDataConfirm` with your plugin name.
You don't need to use `DICTrait`, it is already in use!

Expand you plugin class for installing languages of the library to your plugin
```php
...

	/**
	 * @inheritdoc
	 */
	public function updateLanguages($a_lang_keys = null) {
		parent::updateLanguages($a_lang_keys);

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__ . "/../vendor/srag/removeplugindataconfirm/lang")
			->updateLanguages($a_lang_keys);
	}
...
```

Notice to also adjust `dbupdate.php` so it can be reinstalled if the data should already exists!

If you want to use this library, but don't want to confirm to remove data, you can disable it with add the follow to your `ilXPlugin` class:
```php
//...
const REMOVE_PLUGIN_DATA_CONFIRM = false;
//...
```
### Dependencies
* PHP >=5.6
* [composer](https://getcomposer.org)
* [srag/dic](https://packagist.org/packages/srag/dic)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LRPDC
* Bug reports under https://jira.studer-raimann.ch/projects/LRPDC
* For external users you can report it at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_LRPDC
