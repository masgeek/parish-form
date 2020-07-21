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

    <!--    <link href="vendor/yarn-asset/gijgo/css/gijgo.min.css" rel="stylesheet">-->
    <link href="vendor/yarn-asset/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/fancy-radio-buttons.css" rel="stylesheet">
</head>

<body>
<!-- Start your project here-->
<div class="container-fluid">


    <!-- input form -->
    <div class="card">

        <h5 class="card-header text-center text-white bg-primary">
            <strong>Register</strong>
        </h5>

        <!--Card content-->
        <div class="card-body">
            <!-- Form -->
            <form action="#" id="mass-reg-form" class="was-validated">

                <input type="hidden" id="schedule_id" name="schedule_id" value="<?= $schedule_id ?>" readonly>
                <input type="hidden" id="outstation_id" name="outstation_id" value="<?= $station_id ?>" readonly>

                <!-- Email -->
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" id="surname" class="form-control" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="col-md">
                        <div class="form-group">
                            <label for="other_names">Other names</label>
                            <input type="text" id="other_names" class="form-control" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <label>Are you an adult?</label>
                        <div class="form-group">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="adultFlag" required>Yes
                                </label>
                                <div class="invalid-feedback">Please fill out this field.</div>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="adultFlag" required>No
                                </label>
                                <div class="invalid-feedback">Please fill out this field.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="age">What is your age?</label>
                            <input type="number" id="age" class="form-control" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="mobile">What is your mobile number?</label>
                            <input type="text" id="mobile" class="form-control" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="group">What is the name of your jumuia?</label>
                            <select class="form-control" id="group" name="group" required>
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
                        <div class="form-group">
                            <label for="estate-name">What is your estate name?</label>
                            <input type="text" id="estate-name" name="estate-name" class="form-control" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <h5>Choose your preferred mass</h5>
                        <div class="funkyradio form-group">
                            <?php foreach ($scheduledMasses as $key => $value): ?>
                                <div class="funkyradio-success">
                                    <input type="radio" name="mass" id="defaultChecked-<?= $key ?>" required/>
                                    <label for="defaultChecked-<?= $key ?>">
                                        <?= $value['mass_title'] ?>
                                    </label>
                                    <div class="invalid-feedback">Please fill out this field.</div>
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
<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
<script type="text/javascript" src="js/process-data.js"></script>
</body>

</html>