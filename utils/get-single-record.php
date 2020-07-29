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
if ($isAjax) {


    $id = Request::post('id');

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
        'id' => $id,
    ];

    $data = $conn->selectData('mass_registration', $queryFields, $conditions);

    if ($data) {
        $jsonResp['hasData'] = true;
        $jsonResp['multiData'] = count($data) > 1;
        $jsonResp['data'] = $data[0];
    }

}

echo json_encode($jsonResp);
exit();
