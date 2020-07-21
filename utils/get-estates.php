<?php
define('MyConst', TRUE);
require_once 'Dao.php';

$conn = new Dao();

$groupId = $_GET['group_id'];
$estateId = isset($_GET['estate_id']) ? $_GET['estate_id'] : 0;
$data = $conn->getEstates($estateId);

echo json_encode($data);
