# GeneratePluginInfosHelper Library

Generate plugin infos helper

## Usage

### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/generateplugininfoshelper": ">=1.0.0"
},
```
And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

## GeneratePluginPhpAndXml
Generate `plugin.php` and `plugin.xml` and `LuceneObjectDefinition.xml` for ILIAS plugins from `composer.json`
```json
  "pre-autoload-dump": [
    ...,
      "srag\\GeneratePluginInfosHelper\\H5P\\x\\GeneratePluginPhpAndXml::generatePluginPhpAndXml"
    ]
```

Complete your `composer.json` with
```json
  ...
  "version": "x.y.z",
  ...
  "extra": {
    "ilias_plugin": {
      "id": "x",
      "name" => "X",
      "ilias_min_version": "x.y.z",
      "ilias_max_version": "x.y.z",
      "learning_progress": true | false,
      "lucene_search": true | false,
      "supports_export": true | false,
      "slot": "x/y/z"
      "events": [
        {
          "id": "X/Y",
          "type": "listen|raise"
        }
      ]
    }
  },
  ...
  "authors": [
    {
      "name": "...",
      "email": "...",
      "homepage": "...",
      "role": "Developer"
    }
  ],
  ...
```

## GeneratePluginReadme
Auto generate `README.md`
```json
  "pre-autoload-dump": [
    ...,
     "srag\\GeneratePluginInfosHelper\\H5P\\x\\GeneratePluginReadme::generatePluginReadme"
    ]
```
There is a default template, but you can create your own plugin template and use it instead
```json
  ...
  "extra": {
    ...
    "GeneratePluginReadme": "..."
    ...
  }
  ...
```

## UpdatePluginReadme (Deprecated)
Update ILIAS min./max. versions and min. PHP version and slot path in `README.md`
```json
  "pre-autoload-dump": [
    ...,
     "srag\\GeneratePluginInfosHelper\\H5P\\x\\UpdatePluginReadme::updatePluginReadme"
    ]
```

## Requirements
* PHP >=7.0

## Adjustment suggestions
* External users can report suggestions and bugs at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_LGENPLUGINFHELP
* Adjustment suggestions by pull requests via github
