<?php

use cse\helpers\Request;

$root_dir = dirname(dirname(__FILE__));
define('MyConst', TRUE);

require_once $root_dir . '/vendor/autoload.php';
require_once 'Dao.php';




$phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();

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
    "mass_schedule_id" => "required|numeric",
    "mass_capacity" => "numeric:required",
];

$jsonResp = [
    'valid' => false,
    'data' => [
        'message' => [
            'title' => '',
            'text' => ''
        ]
    ],
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
        $countryCode = '254';
        $surname = Request::post('surname');
        $otherNames = Request::post('other_names');
        $groupId = Request::post('group_id');
        $adult = Request::post('adultFlag');
        $age = Request::post('age');
        $mobileNo = Request::post('mobile', 0);
        $estateName = Request::post('estate_name');
        $massScheduleId = Request::post('mass_schedule_id');
        $capacity = Request::post('mass_capacity');
        $scheduleId = Request::post('schedule_id');

        $surname = preg_replace('/\s+/', '', $surname);
        $trimmedNames = preg_replace('/\s+/', ' ', $otherNames);

        $isValid = false;
        //validate phone number
        try {
            $swissNumberProto = $phoneUtil->parse($mobileNo, "KE");
            $isValid = $phoneUtil->isValidNumber($swissNumberProto);
            $countryCode = $swissNumberProto->getCountryCode();
            $mobileNo = $swissNumberProto->getNationalNumber();
            $mobileNo = "$countryCode$mobileNo";
        } catch (\libphonenumber\NumberParseException $e) {
            $isValid = false;
            $jsonResp['data'] = [
                'message' => [
                    'title' => $e->getMessage(),
                    'text' => 'Mass registration was not successful'
                ]
            ];
        }
        if ($isValid === false) {
            $jsonResp['data'] = [
                'message' => [
                    'title' => "Invalid phone number",
                    'text' => "Your phone number '${mobileNo}' appears to be invalid"
                ]
            ];
        }
        $jsonResp['valid'] = $isValid;

        $seatsLeft = $conn->getSeatsLeft($massScheduleId, $capacity,false);

        $seatNo = $seatsLeft;
        $data = [
            'seat_no' => $seatNo,
            'surname' => strtoupper($surname),
            'other_names' => strtoupper($trimmedNames),
            'adult' => $adult,
            'age' => $age,
            'group_id' => $groupId,
            'estate_name' => $estateName,
            'mobile' => $mobileNo,
            'mass_schedule_id' => $massScheduleId,
            'attended' => false
        ];

        $jsonResp['mass_schedule_id'] = $massScheduleId;
        $jsonResp['seatsLeft'] = "{$seatsLeft} seats left";

        if ($isValid == true) {
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
                    $jsonResp['valid'] = false;
                    $jsonResp['errors'] = $resp['errors'];
                    $jsonResp['data'] = [
                        'message' => [
                            'title' => 'Mass registration failed',
                            'text' => 'Mass registration was not successful'
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
        }
    } else {
        $jsonResp['errors'] = $helper;
    }
}
echo json_encode($jsonResp);
exit();