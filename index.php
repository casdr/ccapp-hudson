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

function createResponse($data=array()) {
	if(isset($_GET['format']) && $_GET['format'] == 'xml') {
		$array = array('data'=>$data);
		$xml = new SimpleXMLElement('<response/>');
		array_walk_recursive($array, array ($xml, 'addChild'));
		print $xml->asXML();
	} else {
		print json_encode($data, JSON_PRETTY_PRINT);
	}
}

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

if(isset($_GET['callback'])) echo $_GET['callback'].'(';
$app->get('/', function () {
  echo 'You shouldn\'t be here!';
});

// Schedules
$app->get('/v1/student/:id/schedule/:week', function ($id, $week) use ($app) {
	if($week == 'ics') {
		$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
		$app->response->headers->set('Content-Disposition', 'attachment; filename=schedule'.$id.'.ics');
		echo infoweb_student::ics($id);
	}
  else createResponse(infoweb_student::main($id, $week));
});
$app->get('/v1/teacher/:id/schedule/:week', function ($id, $week) use($app) {
	if($week == 'ics') {
		$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
		$app->response->headers->set('Content-Disposition', 'attachment; filename=schedule'.$id.'.ics');
		echo infoweb_teacher::ics($id);
	}
  createResponse(infoweb_teacher::main($id, $week));
});
$app->get('/v1/room/:id/schedule/:week', function ($id, $week) {
  createResponse(infoweb_room::main($id, $week));
});
$app->get('/v1/group/:id/schedule/:week', function ($id, $week) {
  createResponse(infoweb_group::main($id, $week));
});

// Lists
$app->get('/v1/list/weeks', function () {
  createResponse(infoweb_weeks::main());
});
$app->get('/v1/list/teachers', function () {
  createResponse(infoweb_list_teachers::main());
});
$app->get('/v1/list/students', function() {
	createResponse(infoweb_list_students::view(false));
});
$app->get('/v1/list/students/ingroups', function() {
	createResponse(infoweb_list_students::view(true));
});
$app->get('/v1/list/rooms', function() {
	createResponse(infoweb_list_rooms::main());
});
$app->get('/v1/list/groups', function() {
	createResponse(infoweb_list_groups::main());
});

// List generators
$app->get('/v1/list/students/create', function() {
	createResponse(infoweb_list_students::save());
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
	createResponse(array('name'=>$name, 'id'=>$id));
});

// Grades
$app->post('/v1/student/:id/grades/:periode', function($id, $periode) {
	$password = $_POST['password'];
	$user = 'cc'.str_replace(array('cc', 'Cc', 'cC', 'CC'), '', $id);
	createResponse(portal_student::main($user, $password, $periode));
});

// Stuff for in the app
$app->get('/v1/app/iotd', function () {
  echo app_iotd::main();
});
$app->get('/v1/app/versions', function () {
	createResponse(array(
		'ccapp_comp' => array(
			'release'=>array(
				'version'=>2.2,
				'url'=>'https://api.ccapp.it/downloads/CCApp.jar'
			)
		),
	));
});

// Run le app
$app->run();
if(isset($_GET['callback'])) echo ');';
