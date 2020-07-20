<?php
require_once 'conn.php';

$conn = new conn();


$data = $conn->getOutStations();
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

<div class="jumbotron card card-image"
     style="background-image: url(img/gradient1.jpg);">
    <div class="text-white text-center py-5 px-4">
        <div>
            <h2 class="card-title h1-responsive pt-3 mb-5 font-bold"><strong>Register for sunday Mass</strong></h2>
            <p class="mx-5 mb-5">Select your out below to continue
            </p>
            <!--            <a class="btn btn-outline-white btn-md"><i class="fas fa-clone left"></i> View project</a>-->
        </div>
    </div>
</div>


<h2 class='mb-3'>Outstations</h2>
<table id="dtBasicExample" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th>Outstation name</th>
        <th>Description</th>
        <th>#</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $key => $value):
        $name = $value['outstation_name'];
        $id = $value['outstation_id'];
        ?>
        <tr>
            <td><?= $value['outstation_name'] ?></td>
            <td><?= $value['description'] ?></td>
            <td>
                <a class="btn btn-outline-success btn-md btn-block" href="book.php?id=<?= $id ?>&name=<?= $name ?>"><i
                            class="fas fa-clone left"></i> Book</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

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