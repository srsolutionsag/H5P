<?php

require_once "vendor/autoload.php";

use srag\Plugins\H5P\Cron\ilH5PCron;

$cron = new ilH5PCron();

$cron->initILIAS($argv);

$cron->run();
