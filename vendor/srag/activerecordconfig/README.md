Simple ActiveRecord config for ILIAS plugins

### Install
For development you should install this library like follow:

Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Libraries/  
cd Customizing/global/plugins/Libraries/  
git clone git@git.studer-raimann.ch:ILIAS/Plugins/ActiveRecordConfig.git ActiveRecordConfig
```

### Usage

#### Composer
First add the follow to your `composer.json` file:
```json
"require": {
  "srag/activerecordconfig": "^0.4.7"
},
```

If your plugin should support ILIAS 5.2 or earlier you need to require `ActiveRecord` like follow in your `composer.json` file:
```json
"autoload": {
    "classmap": [
      "../../../../../../../Services/ActiveRecord/class.ActiveRecord.php",
```
May you need to adjust the relative `ActiveRecord` path

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use config
Declare your config class basically like follow:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfig;
//...
class XConfig extends ActiveRecordConfig {
	//...
	const TABLE_NAME = "db_table_name";
	//...
	const PLUGIN_CLASS_NAME = XPlugin::class;
	//...
}
```
`db_table_name` is the name of your db table.
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).

And now add some configs:
```php
	//...
	const KEY_SOME = "some";
	//...
	const DEFAULT_SOME = "some";
	//...
	/**
	 * @return string
	 */
	public static function getSome() {
		return self::getStringValue(self::KEY_SOME, self::DEFAULT_SOME);
	}

	/**
	 * @param string $some
	 */
	public static function setSome($some) {
		self::setStringValue(self::KEY_SOME, $some);
	}
```

You can now access your config like `XConfig::getSome()` and set it like `XConfig::setSome("some")`.

Internally all values are stored as strings and will casted with appropriates methods

It exists the follow datatypes:

| Datatype  | Methods                                    |
| :-------- | :----------------------------------------- |
| string    | * getStringValue<br>* setStringValue       |
| int       | * getIntegerValue<br>* setIntegerValue     |
| double    | * getDoubleValue<br>* setDoubleValue       |
| bool      | * getBooleanValue<br>* setBooleanValue     |
| timestamp | * getTimestampValue<br>* setTimestampValue |
| json      | * getJsonValue<br>* setJsonValue           |
| null      | * isNullValue<br>* setNullValue            |

### Update steps
Here some example update steps that can help you to migrate your data:
```php
<#2>
<?php
XConfig::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(XConfigOld::TABLE_NAME)) {
	$config = XConfigOld::getConfig();

 	XConfig::setSome($config->getSome());
	///...

	\srag\DIC\DICCache::dic()->database()->dropTable(XConfigOld::TABLE_NAME);
}
?>
```
or
```php
<#2>
<?php
XConfig::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(XConfigOld::TABLE_NAME)) {
	foreach (XConfigOld::get() as $config) {
		/**
		 * @var XConfigOld $config
		 */
		switch($config->getName()) {
			case XConfig::KEY_SOME:
			 	XConfig::setSome($config->getValue());
				break;
			default:
				break;
		}
	}

	\srag\DIC\DICCache::dic()->database()->dropTable(XConfigOld::TABLE_NAME);
}
?>
```
