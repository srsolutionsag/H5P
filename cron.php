<?php

require_once "classes/H5P/class.ilH5PCron.php";

$cron = new ilH5PCron();

$cron->initILIAS($argv);

$cron->run();
