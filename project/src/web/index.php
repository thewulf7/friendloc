<?php
/**
 * friendloc.index.php
 * User: johnnyutkin
 * Date: 18.12.15
 * Time: 11:28
 */

require('../../vendor/autoload.php');

$config_file = require('../config/main.php');
$config      = new \thewulf7\friendloc\components\config\Config($config_file);

$application = new \thewulf7\friendloc\components\Application($config);
$application->run();