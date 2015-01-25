<?php
require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/simplehtmldom.php';
require 'modules/infoweb_main.php';
require 'modules/infoweb_student.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->get('/', function () {
  echo 'Hello.';
});
$app->get('/test', function () {
  infoweb_main::setWeek(5);
  echo infoweb_student::getWhole(115123);
});
$app->run();
