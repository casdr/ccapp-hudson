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

$app->get('/v2/app/message', function() use($app) {
	// last: 5
	createResponse([
		[
			'id' => 5,
			'onetime' => false,
			'type' => 'success',
			'text' => 'Wij wensen iedereen succes in het nieuwe schooljaar! Maak er een mooi jaar van :-)'
		],
		[
			'id' => 2,
			'onetime' => true,
			'type' => 'success',
			'text' => 'Welkom terug op het vernieuwde CCWeb!'
		],
		[
			'id' => 1,
			'onetime' => false,
			'type' => 'warning',
			'text' => '<b>Let op!</b> Door een fout in Zermelo kun je momenteel niet zien welke lessen je hebt. Dit kan in de officiele website ook niet. Het probleem is gemeld.'
		]
	]);
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
$app->get('/v1/app/iotd', function () use($app) {
  createResponse(array('url'=>app_iotd::main()));
});

$app->get('/v2/zportal/settoken/:key', function($key) use($app) {
	$zportal = new Zportal();
	$zportal->setAppKey($key);
	if($zportal->getToken()) {
		setcookie('ztoken', $zportal->token, time()+31536000, "/");
		createResponse([
			'token' => $zportal->token
		]);
	} else {
		$app->halt(403, json_encode(['error'=>'Deze code is niet correct']));
	}
});
$app->get('/v2/zportal/schedule/:week', function($week) use($app) {
	$token = '';
	if(isset($_GET['token'])) $token = $_GET['token'];
	elseif(isset($_COOKIE['ztoken'])) $token = $_COOKIE['ztoken'];

	if($token == '') {
		$app->halt(401, 'Please set the token first');
	}

	if($week == 0) 
		$week = date('W');
	$zportal = new Zportal();
	$zportal->setToken($token);
	$schedule = $zportal->getSchedule($week);
	if($schedule->response->status != 200) {
		if($schedule->response->status == 401)
			$app->halt(401, 'The token is incorrect');

		$app->halt(500, $schedule->response->message);
	}
	createResponse($schedule->response->data);
});

// Run le app
$app->run();
if(isset($_GET['callback'])) echo ');';