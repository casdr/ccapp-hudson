<?php
require 'Slim/Slim.php';
require 'classes/curl.php';
require 'classes/simplehtmldom.php';

// Infoweb
require 'modules/infoweb/infoweb_main.php';
require 'modules/infoweb/infoweb_schedule_student.php';
require 'modules/infoweb/infoweb_schedule_teacher.php';
require 'modules/infoweb/infoweb_schedule_room.php';
require 'modules/infoweb/infoweb_schedule_group.php';
require 'modules/infoweb/infoweb_list_weeks.php';
require 'modules/infoweb/infoweb_list_teachers.php';
require 'modules/infoweb/infoweb_list_students.php';
require 'modules/infoweb/infoweb_list_rooms.php';
require 'modules/infoweb/infoweb_list_groups.php';

// Portal
require 'modules/portal/portal_main.php';
require 'modules/portal/portal_student.php';

// App
require 'modules/app_iotd.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->contentType('application/json');

if(isset($_GET['callback'])) echo $_GET['callback'].'(';
$app->get('/', function () {
  echo 'Hello.';
});

// Schedules
$app->get('/v1/student/:id/schedule/:week', function ($id, $week) {
  echo json_encode(infoweb_student::main($id, $week), JSON_PRETTY_PRINT);
});
$app->get('/v1/teacher/:id/schedule/:week', function ($id, $week) {
  echo json_encode(infoweb_teacher::main($id, $week), JSON_PRETTY_PRINT);
});
$app->get('/v1/room/:id/schedule/:week', function ($id, $week) {
  echo json_encode(infoweb_room::main($id, $week), JSON_PRETTY_PRINT);
});
$app->get('/v1/group/:id/schedule/:week', function ($id, $week) {
  echo json_encode(infoweb_group::main($id, $week), JSON_PRETTY_PRINT);
});

// Lists
$app->get('/v1/list/weeks', function () {
  echo json_encode(infoweb_weeks::main(), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/teachers', function () {
  echo json_encode(infoweb_list_teachers::main(), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/students', function() {
	echo json_encode(infoweb_list_students::view(false), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/students/ingroups', function() {
	echo json_encode(infoweb_list_students::view(true), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/rooms', function() {
	echo json_encode(infoweb_list_rooms::main(), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/groups', function() {
	echo json_encode(infoweb_list_groups::main(), JSON_PRETTY_PRINT);
});

// List generators
$app->get('/v1/list/students/create', function() {
	echo json_encode(infoweb_list_students::save(), JSON_PRETTY_PRINT);
});
$app->get('/v1/list/rooms/create', function() {
	infoweb_list_rooms::generateJson();
});

// Testing
$app->get('/v1/search/student/:id/name', function($id) {
	$name = '';
	foreach(infoweb_list_students::view() as $group) {
		foreach($group['students'] as $student) {
			if(strpos($student['id'], $id) != 0) $name = $student['name'];
		}
	}
	echo json_encode(array('name'=>$name, 'id'=>$id));
});

// Grades
$app->post('/v1/student/:id/grades/:periode', function($id, $periode) {
	$password = $_POST['password'];
	$user = 'cc'.str_replace(array('cc', 'Cc', 'cC', 'CC'), '', $id);
	echo json_encode(portal_student::main($user, $password, $periode), JSON_PRETTY_PRINT);
});

// Stuff for in the app
$app->get('/v1/app/iotd', function () {
  echo app_iotd::main();
});
$app->get('/v1/app/versions', function () {
	echo json_encode(array(
		'ccapp_touch' => array(
			'current'=>'0.8.9',
			'url'=>'https://api.ccapp.it/downloads/ccapp_touch.jar'
		),
		'steven' => array(
			'current'=>'0.1.5',
			'url'=>'https://api.ccapp.it/downloads/steven.jar'
		)
	));
});

// Run le app
$app->run();
if(isset($_GET['callback'])) echo ');';