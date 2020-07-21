<?php
define('MyConst', TRUE);
define('PAGE_TITLE', 'REGISTER TO ATTEND MASS');
require_once 'vendor/autoload.php';
require_once 'utils/Dao.php';


$conn = new Dao();

$massDates = $conn->getActiveMassDates();

$currentDate = Carbon\Carbon::now()->isoFormat('dddd, Do MMMM YYYY');
?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<?php require_once 'includes/header.php'; ?>
<body class="hm-gradient">
<main>
    <!--MDB -->
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card" style="background-image: url(img/gradient1.jpg);">
                    <div class="text-white text-center d-flex align-items-center py-5 px-4 my-5">
                        <div>
                            <h2 class="card-title h1-responsive pt-3 mb-5 font-bold"><strong><?= PAGE_TITLE ?></strong></h2>
                            <h4>Select your preferred mass below</h4>
                            <h2 class="card-title h1-responsive pt-3 mt-5 font-bold"><strong><?= $currentDate ?></strong>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Jumbotron-->
        <div class="jumbotron">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Outstation</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($massDates as $key => $value):
                    $massDate = $value['mass_schedule_date'];
                    $displayDate = \Carbon\Carbon::parse($massDate)->isoFormat('dddd, Do MMMM YYYY');
                    $massStations = $conn->getMassStations($massDate);
                    ?>
                    <tr>
                        <td colspan="2">
                            <!-- nested table -->
                            <?php foreach ($massStations as $stationKey => $stationValue):
                                $scheduleID = $stationValue['id'];
                                $stationID = $stationValue['outstation_id'];
                                $stationName = $stationValue['outstation_name'];
                                ?>
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <strong><?= $displayDate ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= $stationName ?></strong></td>
                                        <td class="text-center">
                                            <a class="btn btn-primary"
                                               href="book.php?schedule_id=<?= $scheduleID ?>&station_id=<?= $stationID ?>">
                                                <i class="fas fa-clone left"></i> Register</a>
                                        </td>
                                    </tr>
                                </table>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <hr class="my-4">


    </div>
    <!--MDB -->

</main>

</body>

<?php require_once 'includes/footer.php'; ?>

</html>