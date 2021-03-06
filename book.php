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
<html lang="en" class="h-100">
<?php require_once 'includes/header.php'; ?>

<body class="h-100">
<!-- Start your project here-->
<div class="container-fluid">


    <!-- success banner -->
    <div class="row h-100 justify-content-center align-items-center hidden mt-5" id="success-card">
        <div class="col-10 col-md-8 col-lg-6">
            <div class="card">
                <div class="thank-you-pop card-body">
                    <img src="img/green-tick.png" alt="">
                    <h1>Thank You!</h1>
                    <h3 class="cupon-pop"><span id="surname-summary">Surname</span>, your registration is successful and
                        you have
                        booked seat no <span id="seat-summary">x</span></h3>
                    <h4>Kindly arrive 30 minutes before mass</h4>
                    <br/>
                    <strong>To cancel text <a href="tel:+254729390188" class="text-info">+254 729 390 188</a></strong>
                    <br/>
                    <a href="index.php" class="btn btn-link">View mass schedules</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end success banner-->

    <!-- input form -->
    <div class="row h-100 justify-content-center mt-3 mb-5" id="mass-card">
        <div class="col-12 col-md-12 col-lg-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Register for mass on <span class="text-info"><?= $displayDate ?></span> at <span
                                class="text-success"><?= $station_name ?></span></h3>
                </div>

                <!--Card content-->
                <div class="card-body">
                    <!-- Form -->
                    <form action="#" id="mass-reg-form" class="needs-validation">

                        <input type="hidden" id="schedule_id" name="schedule_id" value="<?= $schedule_id ?>" readonly>
                        <input type="hidden" id="outstation_id" name="outstation_id" value="<?= $station_id ?>"
                               readonly>
                        <input type="hidden" id="choir_seat_no" name="choir_seat_no" class="form-control" readonly>

                        <section class="prefill-section">
                            <div class="row">
                                <div class="col-md">
                                    <label>Are you an adult?</label>
                                    <div class="form-group">
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input adult" name="adultFlag"
                                                       value="1"
                                                       required>Yes
                                            </label>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input adult" name="adultFlag"
                                                       value="0"
                                                       required>No
                                            </label>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="national_id" id="national-id-label">What is your national id?</label>
                                    <input type="text" id="national_id" name="national_id"
                                           class="form-control prefill-form"
                                           required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>

                            <div class="col-md">
                                <div class="form-group">
                                    <label for="mobile" id="mobile-label">What is your mobile number?</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control prefill-form"
                                           required>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>
                        </div>


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

                        <section class="prefill-section">
                            <div class="row">
                                <div class="col-md">
                                    <label>What is your gender?</label>
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
                                                <input type="radio" class="form-check-input" name="genderFlag"
                                                       value="MALE"
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
                                        <label for="group-id">What is the name of your jumuia?</label>
                                        <select class="form-control" id="group-id" name="group_id" required>
                                            <option value="" selected>Select your jumuia</option>
                                            <?php foreach ($groups as $key => $value): ?>
                                                <option value="<?= $value['group_id'] ?>"><?= $value['group_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php foreach ($groups as $key => $value):
                                            $id = $value['group_id'];
                                            $totalCapacity = $value['estate_id'];
                                            ?>
                                            <input type="hidden" id="estate-<?= $id ?>" value="<?= $totalCapacity ?>"
                                                   class="form-control" readonly>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-md">
                                    <div class="form-group">
                                        <label for="estate-name">What is your estate name?</label>
                                        <input type="text" id="estate_name" name="estate_name" class="form-control"
                                               required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="row">
                            <div class="col-md">
                                <h5>Choose your preferred mass</h5>
                                <div class="funkyradio form-group">
                                    <?php foreach ($scheduledMasses as $key => $value):
                                        $id = $value['id'];
                                        $totalCapacity = $value['capacity'];
                                        $choirCapacity = $value['choir_capacity'];
                                        $lectorSeatNo = $value['lector_seat_no'];
                                        $seatsLeft = $conn->getSeatsLeft($id, $totalCapacity);
                                        $choirSeatsLeft = $conn->getChoirSeatsLeft($id, $choirCapacity);

                                        $congregationSeatsLeft = $seatsLeft - $choirSeatsLeft;
                                        $disabled = $seatsLeft <= 0 ? 'disabled' : '';
                                        $status = $value['mass_status'];
                                        $massTitle = trim($value['mass_title']);
                                        if ($status != 'OPEN') {
                                            $disabled = 'disabled';
                                        }

                                        ?>
                                        <div class="funkyradio-success">
                                            <input type="radio" name="mass_schedule_id" class="mass_schedule"
                                                   id="defaultChecked-<?= $key ?>"
                                                   value="<?= $id ?>" required <?= $disabled ?>/>
                                            <label for="defaultChecked-<?= $key ?>" class="mass-label">
                                                <?= $massTitle ?>
                                            </label>

                                            <?php if ($status != 'OPEN'): ?>
                                                <span class="float-right mx-1 badge badge-danger"><?= $status ?></span>
                                            <?php else: ?>
                                                <span class="float-right mx-1 badge choir-seats <?= $choirSeatsLeft == 0 ? 'badge-danger' : 'badge-primary' ?>"
                                                      id="choir-seats-left-<?= $id ?>"><?= $choirSeatsLeft ?> choir seats left</span>
                                                <span class="float-right mx-1 badge <?= $congregationSeatsLeft == 0 ? 'badge-danger' : 'badge-success' ?>"
                                                      id="seats-left-<?= $id ?>"><?= $congregationSeatsLeft ?> seats left</span>
                                            <?php endif; ?>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                    <?php endforeach; ?>
                                    <input type="hidden" id="mass-capacity" name="mass_capacity" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <label>Are you a choir member for this mass?</label>
                                <div class="form-group">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input choir" name="choirFlag"
                                                   value="1">Yes
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input choir" name="choirFlag"
                                                   value="0">No
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md lector hidden">
                                <label>Are you are the lector for this mass?</label>
                                <div class="form-group">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="lectorFlag"
                                                   value="1">Yes
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="lectorFlag"
                                                   value="0" checked>No
                                        </label>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Register button -->
                        <div class="row">
                            <div class="col-md">
                                <a href="index.php" class="btn btn-link"><i class="fa fa-backward"></i> Previous
                                    page</a>
                            </div>
                            <div class="col-md">
                                <button class="btn btn-outline-success btn-lg btn-block" type="button"
                                        id="btn-register">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Form -->

                </div>

            </div>
        </div>
    </div>
    <!-- end of input form -->

    <!-- bootstrap modal to handle multiple record matches -->
    <div class="modal fade" id="multiRecordModal" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100">Your record exists in our database.
                        <br/>
                        <span class="small">Please pick the record to book mass or click on <span class="text-info">'Add child'</span>
                            to add a child between 13-17 yrs</span>
                    </h4>
                    <button type="button" class="close btn btn-outline-danger" data-dismiss="modal"><span
                                class="fa fa-window-close"></span></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body funkyradio" id="multiRecordMatches">
                </div>

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-success float-left" data-dismiss="modal">Book for the selected
                    </button>
                    <button type="button" class="btn btn-primary float-right" id="add-child">Add child</button>
                </div>

            </div>
        </div>
    </div>
    <!-- bootstrap modal to handle multiple record matches -->

    <!-- bootstrap modal to handle choir seats -->
    <div class="modal fade" id="choirSeatsModal" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header text-center bg-dark text-white">
                    <h4 class="modal-title w-100">Please pick your choir seat, then click on `Close`</h4>
                </div>

                <!-- Modal body -->

                <div class="modal-body bg-white">
                    <div id="choirSeatsContainer">
                        <h3 class="text-center text-danger">Please pick a mass first</h3>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item"><span class="available circle"></span> Seats available</li>
                        <li class="list-group-item"><span class="taken circle"></span> Seats taken</li>
                    </ul>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block" data-dismiss="modal">Close
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- bootstrap modal to handle choir seats -->
</div>
</body>
<?php require_once 'includes/footer.php'; ?>
</html>