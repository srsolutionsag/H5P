# srag/devtools Library for ILIAS Plugins

Add some dev tools to ILIAS plugin config, if core dev mode is enabled, like reload ctrl structure

This project is licensed under the GPL-3.0-only license

## Usage

### Composer

First add the following to your `composer.json` file:

```json
"require": {
  "srag/devtools": ">=1.0.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

### DevToolsCtrl

Add to your plugin class

```php
...
use srag\DevTools\H5P\x\DevToolsCtrl;
...
    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null) : void
    {
        parent::updateLanguages($a_lang_keys);
        ...
        DevToolsCtrl::installLanguages(self::plugin());
    }
...
```

Add to your plugin config class

```php
...
use srag\DevTools\H5P\x\DevToolsCtrl;
...
/**
 * ...
 * @ilCtrl_isCalledBy srag\DevTools\H5P\x\DevToolsCtrl: ilXConfigGUI
 */
class ...
...
    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd) : void
    {
        ...
        switch (strtolower($next_class)) {
            ...
            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
                break;
            ...
        }
        ...
    }
...
    /**
     *
     */
    protected function setTabs() : void
    {
        ...
        DevToolsCtrl::addTabs(self::plugin());
        ...
    }
...
```

## Requirements

* ILIAS 6.0 - 7.999
* PHP >=7.2
