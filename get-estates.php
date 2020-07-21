<?php
require_once 'conn.php';

$conn = new conn();

$groupId = $_GET['group_id'];
$estateId = isset($_GET['estate_id']) ? $_GET['estate_id'] : 0;
$data = $conn->getEstates($estateId);

echo json_encode($data);
