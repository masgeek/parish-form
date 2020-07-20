<?php
require_once 'conn.php';

$conn = new conn();

$groupId = $_GET['group_id'];
$estateId = $_GET['estate_id'];
$data = $conn->getEstates($estateId);

echo json_encode($data);
