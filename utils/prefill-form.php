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
    'data' => [],
];

$isAjax = Request::isAjax();
if ($isAjax) {

    $country = 'KE';
    $nationalId = Request::post('nationalId');
    $mobileNo = Request::post('mobileNumber', 0);

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


    $query = <<<SQL
SELECT DISTINCT
	mass_registration.surname,
	mass_registration.other_names,
	mass_registration.national_id,
	mass_registration.adult,
	mass_registration.is_choir,
	mass_registration.age,
	mass_registration.gender,
	mass_registration.group_id,
	mass_registration.estate_name,
	mass_registration.mobile 
FROM
	mass_registration 
WHERE
	mass_registration.mobile = '$mobileNo' 
	AND mass_registration.national_id = '$nationalId'
SQL;

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
        'national_id' => $nationalId
        //"LIMIT" => 1
    ];

//    $data = $conn->selectData('mass_registration', $queryFields, $conditions);
    $data = $conn->executeQuery($query);

    if ($data) {
        $jsonResp['hasData'] = true;
        $jsonResp['data'] = $data;
    }

}

echo json_encode($jsonResp);
exit();
