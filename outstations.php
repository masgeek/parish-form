<?php
require_once 'Dao.php';

$conn = new Dao();


echo json_encode($conn->getOutStations());

