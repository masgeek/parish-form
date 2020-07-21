<?php
define('MyConst', TRUE);

require_once 'vendor/autoload.php';
require_once 'utils/Dao.php';

$conn = new Dao();

$cleaner = new \voku\helper\AntiXSS();

$station_id = isset($_GET['station_id']) ? $cleaner->xss_clean($_GET['station_id']) : 0;
$schedule_id = isset($_GET['schedule_id']) ? $cleaner->xss_clean($_GET['schedule_id']) : 0;

$date = date('d D M, Y');

$groups = $conn->getGroups($station_id);

$scheduledMasses = $conn->getActiveScheduledMasses($schedule_id);
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Book mass</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="vendor/yarn-asset/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/yarn-asset/smokejs/dist/css/smoke.css" rel="stylesheet">

    <!-- Your custom styles (optional) -->
    <link href="css/style.css?random=<?php echo uniqid("custom_"); ?>" rel="stylesheet">
    <link href="css/fancy-radio-buttons.css?random=<?php echo uniqid("custom_"); ?>" rel="stylesheet">
</head>

<body class="h-100">
<!-- Start your project here-->
<div class="container-fluid h-100">

    <!-- success banner -->
    <div class="row h-100 justify-content-center align-items-center hidden" id="success-card">
        <div class="col-10 col-md-8 col-lg-6">
            <div class="card">
                <div class="thank-you-pop card-body">
                    <img src="img/green-tick.png" alt="">
                    <h1>Thank You!</h1>
                    <h3 class="cupon-pop">Your Mass registration has been received successfully</h3>
                    <br/>
                    <a href="index.php" class="btn btn-success btn-lg">Finish</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end success banner-->

    <!-- input form -->
    <div class="row h-100 justify-content-center align-items-center" id="mass-card">
        <div class="col-12 col-md-12 col-lg-10">
            <div class="card">

                <h5 class="card-header text-center text-white bg-primary">
                    <strong>Register</strong>
                </h5>

                <!--Card content-->
                <div class="card-body">
                    <!-- Form -->
                    <form action="#" id="mass-reg-form" class="needs-validation" data-parsley-validate="">

                        <input type="hidden" id="schedule_id" name="schedule_id" value="<?= $schedule_id ?>" readonly>
                        <input type="hidden" id="outstation_id" name="outstation_id" value="<?= $station_id ?>"
                               readonly>

                        <!-- Email -->
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="surname">Surname</label>
                                    <input type="text" id="surname" name="surname" class="form-control" required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>
                            <!-- Password -->
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="other_names">Other names</label>
                                    <input type="text" id="other_names" name="other_names" class="form-control"
                                           required>
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
                                            <input type="radio" class="form-check-input" name="adultFlag" value="1"
                                                   required>Yes
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="adultFlag" value="0"
                                                   required>No
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
                                    <input type="number" id="age" name="age" class="form-control" required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="mobile">What is your mobile number?</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control" required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="group-id">What is the name of your jumuia?</label>
                                    <select class="form-control" id="group-id" name="group_id" required>
                                        <option value="" selected>Select your jumuia</option>
                                        <?php foreach ($groups as $key => $value): ?>
                                            <option value="<?= $value['group_id'] ?>"><?= $value['group_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php foreach ($groups as $key => $value):
                                        $id = $value['group_id'];
                                        $capacity = $value['estate_id'];
                                        ?>
                                        <input type="hidden" id="estate-<?= $id ?>" value="<?= $capacity ?>"
                                               class="form-control" readonly>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="estate-name">What is your estate name?</label>
                                    <input type="text" id="estate_name" name="estate_name" class="form-control"
                                           required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <h5>Choose your preferred mass</h5>
                                <div class="funkyradio form-group">
                                    <?php foreach ($scheduledMasses as $key => $value):
                                        $id = $value['id'];
                                        $massId = $value['mass_id'];
                                        $capacity = $value['capacity'];
                                        $seatsLeft = $conn->getSeatsLeft($massId, $capacity);

                                        $disabled = $seatsLeft <= 0 ? 'disabled' : '';
                                        ?>
                                        <div class="funkyradio-success">
                                            <input type="radio" name="mass_schedule" class="mass_schedule"
                                                   id="defaultChecked-<?= $key ?>"
                                                   value="<?= $value['id'] ?>" required <?= $disabled ?>/>
                                            <label for="defaultChecked-<?= $key ?>">
                                                <?= trim($value['mass_title']) ?>
                                            </label>
                                            <span class="float-right mx-1 badge badge-info"
                                                  id="seats-left-<?= $id ?>"><?= $seatsLeft ?> seats left</span>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php foreach ($scheduledMasses as $key => $value):
                                        $id = $value['id'];
                                        $capacity = $value['capacity'];
                                        ?>
                                        <input type="hidden" id="mass-capacity-<?= $id ?>" value="<?= $capacity ?>"
                                               readonly>
                                    <?php endforeach; ?>
                                    <input type="hidden" id="mass-capacity" name="mass_capacity" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- Register button -->
                        <button class="btn btn-outline-success btn-rounded btn-block waves-effect btn-lg" type="button"
                                id="btn-register">
                            Register
                        </button>

                    </form>
                    <!-- Form -->

                </div>

            </div>
        </div>
    </div>
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
<script type="text/javascript" src="vendor/yarn-asset/smokejs/dist/js/smoke.js"></script>
<script type="text/javascript" src="vendor/yarn-asset/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="js/process-data.js?random=<?php echo uniqid("custom_"); ?>"></script>
</body>

</html>