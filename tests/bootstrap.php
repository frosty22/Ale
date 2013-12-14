<?php


if (@!include __DIR__ . '/../../../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

$dumper = new Tester\Dumper();

// configure environment
Tester\Environment::setup();
class_alias('Tester\Assert', 'Assert');
date_default_timezone_set('Europe/Prague');


// create temporary directory
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);


$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();


require __DIR__ . "/../src/Ale/Math.php";
require __DIR__ . "/../src/Ale/DateInterval.php";
