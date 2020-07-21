<?php

use cse\helpers\Request;

define('MyConst', TRUE);

require_once 'vendor/autoload.php';
require_once 'Dao.php';

$whoops = new Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
$whoops->register();
$rules = [
    "surname" => "required|string",
    "other_names" => "required|string",
    "estate_name" => "required|string",
    "group_id" => "required|numeric",
    "mobile" => "required|numeric",
    "adultFlag" => "required|numeric",
    "schedule_id" => "required|numeric",
    "age" => "required|numeric",
    "mass_schedule" => "required|numeric",
    "mass_capacity" => "numeric:required",
];

$jsonResp = [
    'valid' => false,
    'data' => [],
    'errors' => [
        new Exception("Invalid request", 500)
    ]
];

$isPost = Request::isAjax();
if ($isPost) {
    $helper = \RequestHelper\RequestHelper::validatePost($rules);
    $conn = new Dao();
    if ($helper === true) {
        $seatNo = 0;
        $surname = Request::post('surname');
        $otherNames = Request::post('other_names');
        $groupId = Request::post('group_id');
        $adult = Request::post('adultFlag');
        $age = Request::post('age');
        $mobileNo = Request::post('mobile');
        $estateName = Request::post('estate_name');
        $massId = Request::post('mass_schedule');
        $capacity = Request::post('mass_capacity');
        $massSchedule = Request::post('schedule_id');

        $seatsLeft = $conn->getSeatsLeft($massId, $capacity);
        $seatNo = $seatsLeft;
        $data = [
            'seat_no' => $seatNo,
            'surname' => strtoupper($surname),
            'othernames' => strtoupper($otherNames),
            'adult' => $adult == 1 ? 'Yes' : 'No',
            'age' => $age,
            'group_id' => $groupId,
            'estate' => $estateName,
            'mobile' => $mobileNo,
            'mass_id' => $massId,
        ];

        $jsonResp['mass_schedule_id'] = $massId;
        $jsonResp['seatsLeft'] = "{$seatsLeft} seats left";

        if ($seatsLeft > 0) {
            $resp = $conn->insertIntoDatabase($data, 'mass_registration');
            if ($resp['hasError'] === false) {
                $jsonResp['valid'] = true;
                $left = $seatsLeft - 1;
                $jsonResp['seatsLeft'] = "{$left} seats left";
                $jsonResp['data'] = [
                    'message' => [
                        'title' => 'Registration completed successfully',
                        'text' => 'Registration completed successfully'
                    ]
                ];
            } else {
                $jsonResp['errors'] = $resp['errors'];
                $jsonResp['data'] = [
                    'message' => [
                        'title' => 'Mass registration failed',
                        'text' => 'Mas registration was not successful'
                    ]
                ];
            }
        } else {
            $jsonResp['data'] = [
                'message' => [
                    'title' => 'The mass is already full',
                    'text' => 'It appears this mass is already full, please choose another one'
                ]
            ];
        }
    } else {
        $jsonResp['errors'] = $helper;
    }
}
echo json_encode($jsonResp);
exit();