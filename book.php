<?php
define('MyConst', TRUE);
define('PAGE_TITLE', 'Mass Registration');

require_once 'vendor/autoload.php';
require_once 'utils/Dao.php';

$conn = new Dao();

$cleaner = new \voku\helper\AntiXSS();

$station_id = isset($_GET['id']) ? $cleaner->xss_clean($_GET['id']) : 0;
$station_name = isset($_GET['sn']) ? $cleaner->xss_clean($_GET['sn']) : '';
$schedule_id = isset($_GET['schedule_id']) ? $cleaner->xss_clean($_GET['schedule_id']) : 0;

$timeStamp = isset($_GET['ts']) ? $cleaner->xss_clean($_GET['ts']) : 0;

$displayDate = date('l, jS F Y', $timeStamp);
$scheduleDate = date('Y-m-d', $timeStamp);

$groups = $conn->getGroups($station_id);

$scheduledMasses = $conn->getActiveScheduledMasses($station_id, $scheduleDate);
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/header.php'; ?>

<body>
<!-- Start your project here-->
<div class="container-fluid">

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
    <div class="row" id="mass-card">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center text-white bg-primary">
                    <strong>Register for mass on <?= $displayDate ?> at <?= $station_name ?></strong>
                    <a href="index.php" class="btn btn-dark float-left">return</a>
                </div>

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

                            <div class="col-md">
                                <label>Specify your gender?</label>
                                <div class="form-group">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="genderFlag"
                                                   value="FEMALE"
                                                   required>Female
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="genderFlag" value="MALE"
                                                   required>Male
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
                                        $capacity = $value['capacity'];
                                        $seatsLeft = $conn->getSeatsLeft($id, $capacity);

                                        $disabled = $seatsLeft <= 0 ? 'disabled' : '';
                                        ?>
                                        <div class="funkyradio-success">
                                            <input type="radio" name="mass_schedule_id" class="mass_schedule"
                                                   id="defaultChecked-<?= $key ?>"
                                                   value="<?= $id ?>" required <?= $disabled ?>/>
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
</body>
<?php require_once 'includes/footer.php'; ?>
</html>