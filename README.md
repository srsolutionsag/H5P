# H5P ILIAS Plugin

Add H5P contents in repository objects

This is an OpenSource project by studer + raimann ag, CH-Burgdorf (https://studer-raimann.ch)

This project is licensed under the GPL-3.0-only license

## Requirements

* ILIAS 5.4.0 - 6.999
* PHP >=7.0

## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/H5P.git H5P
```

Update, activate and config the plugin in the ILIAS Plugin Administration

## Description

For general info to H5P, please see the official website https://h5p.org

### Config

You can manage H5P packages/libraries:

- Refresh H5P package list from official HUB
- View details of H5P packages from official HUB
- Install new H5P packages from official HUB
- Update new H5P packages from official HUB
- Remove installed H5P packages
- Upload and install H5P packages manually from .h5p files
- Config general H5P options

### Repository object

#### As administrator

##### Contents

You can manage H5P contents:

- Add H5P contents from installed H5P packages
- Edit H5P contents
- Remove H5P contents
- Order H5P contents
- Import H5P contents from .h5p files
- Export H5P contents to .h5p files

##### Results

You can manage H5P content results:

- View result points of users which has solved H5P contents
- Delete results

##### Settings

###### Solve contents only once

With this mode you prevent users can solve H5P contents multiple

But may you need to be sure to disable retry options in H5P contents, to make this function working

#### As user

You can view H5P contents and solve H5P contents

### Page component editor

You can add H5P contents in the page component editor with the [H5PPageComponent](https://github.com/studer-raimann/H5PPageComponent) plugin

### Cron jobs

Look at the [H5PCron](https://github.com/studer-raimann/H5PCron) plugin

## Adjustment suggestions

You can report bugs or suggestions at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_PLH5P

## ILIAS Plugin SLA

We love and live the philosophy of Open Source Software! Most of our developments, which we develop on behalf of customers or on our own account, are publicly available free of charge to all interested parties at https://github.com/studer-raimann.

Do you use one of our plugins professionally? Secure the timely availability of this plugin for the upcoming ILIAS versions via SLA. Please inform yourself under https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Please note that we only guarantee support and release maintenance for institutions that sign a SLA.
