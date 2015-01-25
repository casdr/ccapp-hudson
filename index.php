<?php
require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/simplehtmldom.php';
require 'modules/infoweb_main.php';
require 'modules/infoweb_student.php';
require 'modules/infoweb_teacher.php';
require 'modules/infoweb_room.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->get('/', function () {
  echo 'Hello.';
});
$app->get('/v1/student/schedule/:id/:week', function ($id, $week) {
  echo json_encode(infoweb_student::main($id, $week));
});
$app->get('/v1/teacher/schedule/:id/:week', function ($id, $week) {
  echo json_encode(infoweb_teacher::main($id, $week));
});
$app->get('/v1/room/schedule/:id/:week', function ($id, $week) {
  echo json_encode(infoweb_room::main($id, $week));
});
$app->run();
