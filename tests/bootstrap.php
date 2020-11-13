<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
define('EVERON_LOGGER_TESTS_FIXTURE_DIR', PROJECT_ROOT . '/tests/fixtures/');

require_once PROJECT_ROOT . '/vendor/autoload.php';
