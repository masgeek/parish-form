<?php
require_once 'conn.php';

$conn = new conn();


echo json_encode($conn->getOutStations());

