<?php

use cse\helpers\Request;
use libphonenumber\NumberParseException;
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
    'multiData' => false,
    'data' => [],
];

$isAjax = Request::isAjax();
if ($isAjax) {

    $country = 'KE';
    $nationalId = Request::post('nationalId');
    $mobileNo = Request::post('mobileNumber', 0);

//
//    $nationalId = '20401185';
//    $mobileNo = '254721630629';

    try {
        $swissNumberProto = $phoneUtil->parse($mobileNo, $country);
        $isValid = $phoneUtil->isValidNumber($swissNumberProto);
        $countryDiallingCode = $swissNumberProto->getCountryCode();
        $mobileNo = $swissNumberProto->getNationalNumber();
        $mobileNo = "$countryDiallingCode$mobileNo";
    } catch (NumberParseException $e) {
        $jsonResp['data'] = $e->getMessage();
    }


    $queryFields = [
        'id',
        'surname',
        'other_names',
        'national_id',
        'adult',
        'is_choir',
        'age',
        'gender',
        'group_id',
        'estate_name',
        'mobile',
    ];

    $conditions = [
        'mobile' => $mobileNo,
        'national_id' => $nationalId,
        //"LIMIT" => 1
    ];

    $data = $conn->selectData('mass_registration', $queryFields, $conditions);

    if ($data) {
        $jsonResp['hasData'] = true;
        $jsonResp['multiData'] = count($data) > 1;
        $jsonResp['data'] = $data;
    }

}

echo json_encode($jsonResp);
exit();
