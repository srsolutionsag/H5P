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
  "srag/activerecordconfig": "^0.4.1"
},
```
And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use config
Declare your config class basically like follow:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfig;
//...
class ilXConfig extends ActiveRecordConfig {
	//...
	const TABLE_NAME = "db_table_name";
	//...
	const PLUGIN_CLASS_NAME = ilXPlugin::class;
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

You can now access your config like `ilXConfig::getSome()` and set it like `ilXConfig::setSome("some")`.

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
ilXConfig::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(ilXConfigOld::TABLE_NAME)) {
	$config = ilXConfigOld::getConfig();

	ilXConfig::setSome($config->getSome());
	///...

	\srag\DIC\DICCache::dic()->database()->dropTable(ilXConfigOld::TABLE_NAME);
}
?>
```
or
```php
<#2>
<?php
ilXConfig::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(ilXConfigOld::TABLE_NAME)) {
	foreach (ilXConfigOld::get() as $config) {
		/**
		 * @var ilXConfigOld $config
		 */
		ilXConfig::setStringValue($config->getName(), $config->getValue());
	}

	\srag\DIC\DICCache::dic()->database()->dropTable(ilXConfigOld::TABLE_NAME);
}
?>
```
