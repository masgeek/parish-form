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
    "national_id" => "required|string",
    "mobile" => "required|string",
    "adultFlag" => "required|numeric",
    "genderFlag" => "required|string",
    "lectorFlag" => "required|string",
    "schedule_id" => "required|numeric",
    "age" => "required|numeric",
    "mass_schedule_id" => "required|numeric"
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

$isAjax = Request::isAjax();
if ($isAjax) {
    $helper = \RequestHelper\RequestHelper::validatePost($rules);
    $conn = new Dao();
    if ($helper === true) {
        $seatNo = 0;
        $isValid = false;
        $choirFull = false;
        $seatTaken = false;
        $lectorAssigned = false;
        $country = 'KE';

        $surname = Request::post('surname');
        $otherNames = Request::post('other_names');
        $nationalId = Request::post('national_id');
        $groupId = Request::post('group_id');
        $adultFlag = (int)Request::post('adultFlag');
        $gender = Request::post('genderFlag');
        $choirFlag = (int)Request::post('choirFlag');
        $lectorFlag = (int)Request::post('lectorFlag', 0);
        $age = (int)Request::post('age');
        $mobileNo = Request::post('mobile', 0);
        $choirSeatNo = (int)Request::post('choir_seat_no', 0);
        $estateName = Request::post('estate_name');
        $massScheduleId = (int)Request::post('mass_schedule_id');
        $scheduleId = (int)Request::post('schedule_id');

        $surname = preg_replace('/\s+/', '', $surname);
        $trimmedNames = preg_replace('/\s+/', ' ', $otherNames);

        try {
            $swissNumberProto = $phoneUtil->parse($mobileNo, $country);
            $isValid = $phoneUtil->isValidNumber($swissNumberProto);
            $countryDiallingCode = $swissNumberProto->getCountryCode();
            $mobileNo = $swissNumberProto->getNationalNumber();
            $mobileNo = "{$countryDiallingCode}{$mobileNo}";
        } catch (\libphonenumber\NumberParseException $e) {
            $isValid = false;
        }


        $jsonResp['valid'] = $isValid;

        if ($isValid === false) {
            $jsonResp['data'] = [
                'message' => [
                    'title' => "Invalid phone number",
                    'text' => "Your phone number '${mobileNo}' appears to be invalid"
                ]
            ];
            echo json_encode($jsonResp);
            exit();
        }
        $lectorSeatNumber = $conn->getLectorSeat($massScheduleId);
        $massCapacity = $conn->getMassScheduleCapacity($massScheduleId);
        $choirCapacity = $conn->getMassScheduleChoirCapacity($massScheduleId);

        $allSeatNumbersArr = $conn->getSeatsArray($massCapacity, $lectorSeatNumber);
        $choirSeatsArr = $conn->getSeatsArray($choirCapacity, $lectorSeatNumber);
        $publicSeatsArr = array_values(array_diff($allSeatNumbersArr, $choirSeatsArr));


        if ($choirFlag === 1) {
            $assignedSeatsArr = $conn->getAllocatedSeats($scheduleId, 1);
            $seatsAvailableArr = array_values(array_diff($choirSeatsArr, $assignedSeatsArr));

            if (empty($seatsAvailableArr)) {
                $seatsLeft = 0;
                $choirFull = true;
                $isValid = false;
            } else {
                $seatsLeft = count($seatsAvailableArr);
                if (in_array($choirSeatNo, $seatsAvailableArr)) {
                    $seatNo = $choirSeatNo;
                } else {
                    $isValid = false;
                    $seatTaken = true;
                }
            }
            $jsonResp['choirSeatsLeft'] = "{$seatsLeft} choir seats left";
        } else {
            $assignedSeatsArr = $conn->getAllocatedSeats($scheduleId);
            $seatsAvailableArr = array_values(array_diff($publicSeatsArr, $assignedSeatsArr));
            if (empty($seatsAvailableArr)) {
                $seatsLeft = 0;
            } else {
                $seatsLeft = count($seatsAvailableArr);
                $seatNo = $seatsAvailableArr[0];
            }

            $jsonResp['seatsLeft'] = "{$seatsLeft} seats left";
        }

        if ($lectorFlag === 1) {
            $lectorAssigned = $conn->isLectorSeatAssigned($massScheduleId);
            $seatNo = $lectorSeatNumber;
            if ($lectorAssigned) {
                $jsonResp['valid'] = false;
                $jsonResp['data'] = [
                    'message' => [
                        'title' => 'Lector already assigned',
                        'text' => 'It appears the lector seat has already been assigned, please change your options'
                    ]
                ];
                echo json_encode($jsonResp);
                exit();
            }
        }
        $data = [
            'seat_no' => $seatNo,
            'surname' => strtoupper($surname),
            'other_names' => strtoupper($trimmedNames),
            'national_id' => $nationalId,
            'adult' => $adultFlag,
            'is_choir' => $choirFlag,
            'is_lector' => $lectorFlag,
            'age' => $age,
            'gender' => $gender,
            'group_id' => $groupId,
            'estate_name' => $estateName,
            'mobile' => $mobileNo,
            'mass_schedule_id' => $massScheduleId,
            'attended' => false
        ];

        $jsonResp['mass_schedule_id'] = $massScheduleId;

        $isRegistered = $conn->isAlreadyRegistered($data['mass_schedule_id'], $data['surname'], $data['other_names'], $data['mobile']);

        if ($isRegistered) {
            $jsonResp['valid'] = false;
            $jsonResp['data'] = [
                'message' => [
                    'title' => 'Already registered',
                    'text' => 'It appears you have already registered for this mass, please try another one'
                ]
            ];
            echo json_encode($jsonResp);
            exit();
        }

        if ($isValid == true) {
            if ($seatsLeft > 0) {
                $resp = $conn->insertIntoDatabase($data, 'mass_registration');
                if ($resp['hasError'] === false) {
                    $jsonResp['valid'] = true;
                    $left = $seatsLeft - 1;
                    $jsonResp['seatsLeft'] = "{$left} seats left";
                    $jsonResp['data'] = [
                        'surname' => $surname,
                        'seatNo' => $seatNo,
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
                $jsonResp['valid'] = false;
                if ($seatTaken) {
                    $jsonResp['data'] = [
                        'message' => [
                            'title' => "Seat taken",
                            'text' => "The selected seat number {$choirSeatNo} has already been reserved"
                        ]
                    ];
                } else {
                    $jsonResp['data'] = [
                        'message' => [
                            'title' => $choirFull ? 'Choir seats full' : 'The mass is already full',
                            'text' => $choirFull ? 'Please select non choir option to get assigned normal seats' : 'It appears this mass is already full, please choose another one'
                        ]
                    ];
                }
            }
        } else {
            $jsonResp['valid'] = false;
            $jsonResp['data'] = [
                'message' => [
                    'title' => 'Mass registration failed',
                    'text' => 'Mass registration was not successful, please try again'
                ]
            ];
        }
    } else {
        $jsonResp['errors'] = $helper;
    }
}
echo json_encode($jsonResp);
exit();