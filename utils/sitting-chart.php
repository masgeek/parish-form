<?php

use cse\helpers\Request;
use Whoops\Handler\JsonResponseHandler;

$root_dir = dirname(dirname(__FILE__));
define('MyConst', TRUE);

require_once $root_dir . '/vendor/autoload.php';
require_once 'Dao.php';

$phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();

$whoops = new Whoops\Run();
$whoops->pushHandler(new JsonResponseHandler());
$whoops->register();

$conn = new Dao();


$jsonResp = [
    'hasData' => false,
    'data' => [],
];

$isAjax = Request::isAjax();
//if ($isAjax) {

$scheduleId = Request::post('schedule_id');

$queryFields = [
    'capacity',
    'choir_capacity',
    'lector_seat_no'
];

$conditions = [
    'id' => $scheduleId,
];

$data = $conn->selectData('mass_schedule', $queryFields, $conditions);

if ($data) {
    //get the value
    $choirCapacity = (int)$data[0]['choir_capacity'];

    $seatData = [];
    $assignedSeatsArr = $conn->getAllocatedSeats($scheduleId, 1);
    $voices = [
        'soprano', 'alto', 'tenor', 'bass'
    ];

    for ($seatNo = 1; $seatNo <= $choirCapacity; $seatNo++) {
        $seatData[] = [
            'taken' => in_array($seatNo, $assignedSeatsArr), //check to see if the seat is assigned
            'seatNo' => $seatNo,
            'assigned' => $assignedSeatsArr
        ];
    }

    $seatPerRow = round(sizeof($seatData) / 4);
    $seats = array_chunk($seatData, $seatPerRow);
    $jsonResp['hasData'] = true;
    $jsonResp['seatPerRow'] = $seatPerRow;
    $jsonResp['totalSeats'] = sizeof($seatData);
    $jsonResp['data'] = $seats;
}

//}

echo json_encode($jsonResp);
exit();
