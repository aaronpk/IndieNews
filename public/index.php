<?php
use Slim\Factory\AppFactory;

chdir(__DIR__.'/../');
require 'vendor/autoload.php';
require 'lib/config.php';

// Set up the database connection
ORM::configure('mysql:host=' . Config::$dbHost . ';dbname=' . Config::$dbName . ';charset=utf8mb4');
ORM::configure('username', Config::$dbUsername);
ORM::configure('password', Config::$dbPassword);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));

$app = AppFactory::create();

require 'controllers/middleware.php';
require 'controllers/static.php';
require 'controllers/webmention.php';
require 'controllers/controllers.php';

session_start();

$app->run();
