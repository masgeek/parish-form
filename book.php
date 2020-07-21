<?php
require_once 'conn.php';

$conn = new conn();

$station_id = isset($_GET['station_id']) ? $_GET['station_id'] : 0;
$schedule_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : 0;

$date = date('d D M, Y');

$groups = $conn->getGroups($station_id);

$scheduledMasses = $conn->getActiveScheduledMasses($schedule_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Book mass</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="vendor/yarn-asset/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="vendor/yarn-asset/mdbootstrap/css/mdb.min.css" rel="stylesheet">
    <!--    <link href="vendor/yarn-asset/gijgo/css/gijgo.min.css" rel="stylesheet">-->
    <link href="vendor/yarn-asset/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/fancy-radio-buttons.css" rel="stylesheet">
</head>

<body>
<!-- Start your project here-->
<div class="container-fluid">
    <!--    <div class="jumbotron card card-image"-->
    <!--         style="background-image: url(img/gradient1.jpg);">-->
    <!--        <div class="text-white text-center py-5 px-4">-->
    <!--            <div>-->
    <!--                <h1 class="card-title h1-responsive pt-3 mb-5 font-bold"><strong>-->
    <? //= $name ?><!--</strong></h1>-->
    <!--                <h3 class="card-subtitle h2-responsive pt-3 mb-5 font-bold"><strong>-->
    <? //= $date ?><!--</strong></h3>-->
    <!--                <p class="mx-5 mb-5">Please provide details below to continue-->
    <!--                </p>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->


    <!-- input form -->
    <!-- Material form login -->
    <div class="card">

        <h5 class="card-header info-color white-text text-center py-4">
            <strong>Register</strong>
        </h5>

        <!--Card content-->
        <div class="card-body px-lg-5 pt-0">

            <!-- Form -->
            <form style="color: #757575;" action="#" id="mass-reg-form">

                <input type="text" id="schedule_id" name="schedule_id" value="<?= $schedule_id ?>" readonly>
                <input type="text" id="outstation_id" name="outstation_id" value="<?= $station_id ?>" readonly>

                <!-- Email -->
                <div class="row">
                    <div class="col-md">
                        <div class="md-form">
                            <input type="text" id="surname" class="form-control">
                            <label for="surname">Surname</label>
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="col-md">
                        <div class="md-form">
                            <input type="text" id="other_names" class="form-control">
                            <label for="other_names">Other names</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <h5>Are you an adult?</h5>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="rdYes"
                                   name="rdNo">
                            <label class="custom-control-label" for="rdYes">YES</label>
                        </div>

                        <!-- Default inline 2-->
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="rdNo"
                                   name="rdNo">
                            <label class="custom-control-label" for="rdNo">NO</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="md-form">
                            <input type="text" id="age" class="form-control">
                            <label for="age" class="h5">What is your age?</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="md-form">
                            <input type="text" id="mobile" class="form-control">
                            <label for="mobile">What is your mobile number?</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="md-form">
                            <h5>What is the name of your jumuia?</h5>
                            <select class="browser-default custom-select custom-select-md mb-3" id="group" name="group">
                                <option selected>Select your jumuia</option>
                                <?php foreach ($groups as $key => $value): ?>
                                    <option value="<?= $value['group_id'] ?>"><?= $value['group_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php foreach ($groups as $key => $value):
                                $groupId = $value['group_id'];
                                $estateId = $value['estate_id'];
                                $groupName = $value['group_name'] . '--' . $value['estate_id'];
                                ?>
                                <input type="hidden" id="estate-<?= $groupId ?>" value="<?= $estateId ?>"
                                       class="form-control" readonly>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="md-form">
                            <input type="text" id="estate" class="form-control">
                            <label for="estate">What is your estate name?</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="funkyradio">
                        <?php foreach ($scheduledMasses as $key => $value): ?>
                            <div class="funkyradio-success">
                                <input type="radio" name="mass" id="defaultChecked-<?= $key ?>"/>
                                <label for="defaultChecked-<?= $key ?>">
                                    <?= $value['mass_title'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
                </div>
                <!-- Register button -->
                <button class="btn btn-outline-success btn-rounded btn-block waves-effect" type="button"
                        id="btn-register">
                    Register
                </button>

            </form>
            <!-- Form -->

        </div>

    </div>
    <!-- Material form login -->
    <!-- end of input form -->
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
<!--<script type="text/javascript" src="vendor/yarn-asset/gijgo/js/gijgo.min.js"></script>-->
<script type="text/javascript" src="vendor/yarn-asset/flatpickr/dist/flatpickr.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
<script type="text/javascript" src="js/process-data.js"></script>
</body>

</html>