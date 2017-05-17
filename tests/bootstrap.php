<?php
require(__DIR__.'/../vendor/autoload.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include dirname(__DIR__) . '/src/Consts/Folder.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/src/Consts/Scopes.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/src/Consts/Http.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/src/Consts/Events.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/src/Consts/LogLevel.php'; // Tweak for "use const" works. Need better autoload
