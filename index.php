<?php
define('MyConst', TRUE);
define('PAGE_TITLE', 'REGISTER TO ATTEND MASS');
require_once 'utils/Dao.php';


$conn = new Dao();

$massDates = $conn->getActiveMassDates();

$currentDate = date('l, jS F Y');

?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<?php require_once 'includes/header.php'; ?>
<body class="h-100">

<noscript>
    <style type="text/css">
        .pagecontainer {
            display: none;
        }
    </style>
    <div class="noscriptmsg">
        <h1>You don't have javascript enabled. Good luck with that.</h1>
    </div>
</noscript>
<!-- Start your project here-->
<div class="container-fluid pagecontainer h-100">
    <div class="row h-100 justify-content-center">
        <div class="col-10 col-md-10 col-lg-10">
            <div class="jumbotron" style="background-image: url(img/gradient1.jpg);">
                <div class="text-white text-center mt-5">
                    <div>
                        <h2 class="card-title h1-responsive pt-3 mb-5 font-bold"><strong><?= PAGE_TITLE ?></strong></h2>
                        <h4>Select your preferred mass below</h4>
                        </h2>
                    </div>
                </div>
            </div>

            <table class="table">
                <thead>
                <tr class="bg-success text-white">
                    <th>Outstation</th>
                    <th class="text-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($massDates as $key => $value):
                    $massDate = $value['mass_schedule_date'];

                    $timeStamp = strtotime($massDate);
                    $displayDate = date('l, jS F Y', $timeStamp);

                    $massStations = $conn->getMassStations($massDate);
                    ?>
                    <tr>
                        <td colspan="2" class="text-center bg-info text-white">
                            <strong><?= $displayDate ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <!-- nested table -->
                            <?php foreach ($massStations as $stationKey => $stationValue):
                                $stationID = $stationValue['outstation_id'];
                                $stationName = $stationValue['outstation_name'];
                                ?>
                                <table class="table table-sm table-hover">
                                    <tr>
                                        <td class="description"><?= $stationName ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-block"
                                               href="book.php?id=<?= $stationID ?>&ts=<?= $timeStamp ?>&sn=<?=$stationName?>">
                                                <i class="fas fa-clipboard-list left"></i> Register</a>
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
    </div>
</div>
<!-- /Start your project here-->
</body>

<?php require_once 'includes/footer.php'; ?>

</html>