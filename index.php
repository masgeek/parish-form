<?php
require_once 'conn.php';

$conn = new conn();


$massDates = $conn->getActiveMassDates();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Material Design Bootstrap</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="vendor/yarn-asset/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="vendor/yarn-asset/mdbootstrap/css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<!-- Start your project here-->
<div class="container-fluid">
    <div class="jumbotron card card-image"
         style="background-image: url(img/gradient1.jpg);">
        <div class="text-white text-center py-5 px-4">
            <div>
                <h2 class="card-title h1-responsive pt-3 mb-5 font-bold"><strong>Kindly Register for Mass at the
                        outsation of your choice </strong></h2>
            </div>
        </div>
    </div>


    <h2 class='mb-3'>Outstations</h2>
    <table class="table table-bordered">
        <tbody>
        <?php foreach ($massDates as $key => $value):
            $massDate = $value['mass_schedule_date'];
            $massStations = $conn->getMassStations($massDate);

            ?>
            <tr>
                <th><h2><?= $massDate ?></h2></th>
                <th>&nbsp;</th>
                <!-- nested table -->
                <?php foreach ($massStations as $stationKey => $stationValue):
                    $id = $stationValue['id'];
                    ?>
                    <table class="table table-striped">
                        <tr>
                            <td class="col-sm-12"><?= $stationValue['outstation_name'] ?></td>
                            <td>
                                <a class="btn btn-outline-success btn-sm"
                                   href="book.php?station_id=<?= $id ?>">
                                    <i class="fas fa-clone left"></i> Register</a>
                            </td>
                        </tr>
                    </table>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- /Start your project here-->

<!-- SCRIPTS -->
<!-- JQuery -->
<script type="text/javascript" src="vendor/yarn-asset/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap tooltips -->
<script type="text/javascript" src="vendor/yarn-asset/popper.js/dist/popper.min.js"></script>
<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="vendor/yarn-asset/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- MDB core JavaScript -->
<script type="text/javascript" src="vendor/yarn-asset/mdbootstrap/js/mdb.min.js"></script>

<script type="text/javascript" src="js/process-data.js"></script>
</body>

</html>