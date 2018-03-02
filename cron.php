<?php

require_once "vendor/autoload.php";

$cron = new ilH5PCron();

$cron->initILIAS($argv);

$cron->run();
