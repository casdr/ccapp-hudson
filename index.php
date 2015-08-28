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

//Zportal
require 'modules/zportal/zportal_main.php';

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


// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    http_response_code(200);
}

if(isset($_GET['callback'])) echo $_GET['callback'].'(';
/*
// Schedules
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

// Stuff for in the app
$app->get('/v1/app/iotd', function () use($app) {
  createResponse(array('url'=>app_iotd::main()));
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
*/

// v1
// $app->group('/v1', function() use($app) {

// 	$app->group('/student/:id', function($id) use($app) {

// 		$app->get('/schedule/:week', function ($id, $week) use ($app) {
// 			if($week == 'ics') {
// 				$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
// 				$app->response->headers->set('Content-Disposition', 'attachment; filename=schedule'.$id.'.ics');
// 				echo infoweb_student::ics($id);
// 			}
// 		  else createResponse(infoweb_student::main($id, $week));
// 		});
// 		$app->post('/grades/:periode', function($id, $periode) {
// 			$password = $_POST['password'];
// 			$user = 'cc'.str_replace(array('cc', 'Cc', 'cC', 'CC'), '', $id);
// 			createResponse(portal_student::main($user, $password, $periode));
// 		});

// 	});
// 	$app->group('/teacher/:id', function($id) use($app) {
// 		$app->get('/schedule/:week', function ($id, $week) use($app) {
// 			if($week == 'ics') {
// 				$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
// 				$app->response->headers->set('Content-Disposition', 'attachment; filename=schedule'.$id.'.ics');
// 				echo infoweb_teacher::ics($id);
// 			}
// 		  createResponse(infoweb_teacher::main($id, $week));
// 		});
// 	});

// });



$app->get('/v2/student/settoken/:key', function($key) use($app) {
	$zportal = new Zportal();
	$zportal->setAppKey($key);
	$zportal->getToken();
	setcookie('ztoken', $zportal->token, time()+45862485, "/");
	createResponse([
		'token' => $zportal->token
	]);
});
$app->get('/v2/student/schedule/:week', function($week) {
	if($week == 0) 
		$week = date('W');
	$zportal = new Zportal();
	$zportal->setToken($_COOKIE['ztoken']);
	$schedule = $zportal->getSchedule($week);
	createResponse($schedule);
});

// Run le app
$app->run();
if(isset($_GET['callback'])) echo ');';