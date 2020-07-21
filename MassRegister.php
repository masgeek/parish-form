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

        $seatCount = $conn->getAllocatedSeatCount($massId);

        $seatsLeft = $capacity - $seatCount;

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

        if ($seatsLeft > 0) {
            $resp = $conn->insertIntoDatabase($data, 'mass_registration');
            if ($resp['hasError'] === false) {
                $jsonResp['valid'] = true;
                $jsonResp['data'] = [
                    'message' => 'Registration completed successfully'
                ];
            } else {
                $jsonResp['errors'] = $resp['errors'];
                $jsonResp['data'] = [
                    'message' => 'Registration completed successfully'
                ];
            }
        } else {
            $jsonResp['data'] = [
                'message' => 'The mass is already full'
            ];
        }
    } else {
        $jsonResp['errors'] = $helper;
    }
}
echo json_encode($jsonResp);
exit();