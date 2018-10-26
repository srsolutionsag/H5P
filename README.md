## Installation

### Install H5P repository plugin
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/H5P.git H5P
```
Update and activate the plugin in the ILIAS Plugin Administration

Also have a look to [H5PCron](https://github.com/studer-raimann/H5PCron) and [H5PPageComponent](https://github.com/studer-raimann/H5PPageComponent).

### Documentation
Click [here](./doc/Documentation.pdf) for a more detailed documentation.

### Dependencies
* ILIAS 5.2 or ILIAS 5.3
* PHP >=5.6
* [composer](https://getcomposer.org)
* [H5PCron](https://github.com/studer-raimann/H5PCron)
* [H5PPageComponent](https://github.com/studer-raimann/H5PPageComponent)
* [h5p/h5p-core](https://packagist.org/packages/h5p/h5p-core)
* [h5p/h5p-editor](https://packagist.org/packages/h5p/h5p-editor)
* [srag/activerecordconfig](https://packagist.org/packages/srag/activerecordconfig)
* [srag/custominputguis](https://packagist.org/packages/srag/custominputguis)
* [srag/dic](https://packagist.org/packages/srag/dic)
* [srag/removeplugindataconfirm](https://packagist.org/packages/srag/removeplugindataconfirm)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/H5P/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/PLH5P
* Bug reports under https://jira.studer-raimann.ch/projects/PLH5P
* For external users please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this plugin you should install this plugin like follow:

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone -b develop git@git.studer-raimann.ch:ILIAS/Plugins/H5P.git H5P
```

### ILIAS Plugin SLA
Wir lieben und leben die Philosophie von Open Source Software! Die meisten unserer Entwicklungen, welche wir im Kundenauftrag oder in Eigenleistung entwickeln, stellen wir öffentlich allen Interessierten kostenlos unter https://github.com/studer-raimann zur Verfügung.

Setzen Sie eines unserer Plugins professionell ein? Sichern Sie sich mittels SLA die termingerechte Verfügbarkeit dieses Plugins auch für die kommenden ILIAS Versionen. Informieren Sie sich hierzu unter https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Bitte beachten Sie, dass wir nur Institutionen, welche ein SLA abschliessen Unterstützung und Release-Pflege garantieren.
