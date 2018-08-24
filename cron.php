<?php

require_once "vendor/autoload.php";

use srag\Plugins\H5P\Cron\H5PCron;

$cron = new H5PCron();

$cron->initILIAS($argv);

$cron->run();
