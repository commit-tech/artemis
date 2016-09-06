<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }	
        
?>
<?php include_once(dirname(__FILE__).'/Controller/TrackingController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php
    $HTML_NO = "&#10008";
    $HTML_YES = "&#10004";
    $monthName = ["","January","February","March","April","May","June","July",
                  "August","September","October","November","December"];
    $userID = $_SESSION['user_id'];
    $trackingController = TrackingController::getInstance();
    $userController = UserController::getInstance();
    $month = 0;
    $year = 0;
    if (isset($_GET["submit"]) && $_GET["submit"] == "yes" && 
        isset($_GET["month"]) && isset($_GET["year"])) {
        $month = $_GET["month"];
        $year = $_GET["year"];
        $trackingStatus = $userController->getTrackingStatus($_SESSION['user_id']);
        if (isset($_GET['create']) && $_GET['create'] == 'yes') {
            $trackingController->addTrackingMonth($month,$year);
        }
        if (isset($_GET['switch']) && isset($_GET['indexNo']) && ($_GET['switch'] == 0 || $_GET['switch'] == 1)) {
            if ($trackingStatus == 'treasurer' || $trackingStatus == 'comcen') {
                $trackingController->changeTracking($trackingStatus,$month,$year,$_GET['indexNo'],$_GET['switch']);
            }
        }
        $trackingData = $trackingController->getMonthData($month,$year);
        if (count($trackingData) == 0) {
            $_SESSION['error'] = 'Tracking not available yet';
        }
        $trackingDefault = $trackingController->getTrackingDefault();
        for ($i = 0; $i < count($trackingData); ++$i) {
            for ($j = 0; $j < count($trackingDefault); ++$j) {
                if ($trackingData[$i]['indexNo'] == $trackingDefault[$j]['indexNo']) {
                    $trackingData[$i]['progress'] = $trackingDefault[$j]['progress'];
                    break;
                }
            }
        }
        $currentStep = $trackingController->getCurrentStep($month,$year);
    }
?>

<html>
    <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NUSSU commIT</title>
        <link href="includes/css/bootstrap.min.css" rel="stylesheet">
        <link href="includes/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="includes/css/style.css" rel="stylesheet">
        <link href="includes/css/tracking.css" rel="stylesheet">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php $page = "tracking"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
        ?>
		<div class="container">
        <h1>Duty Money Tracking System</h1>
        <form method="get" class="form-horizontal well">
            <div class="form-group">
                <label for="month" class="col-sm-2 control-label">Month</label>
				<div class="col-sm-4">
				<select class="form-control" id="month" name="month">
					<option value="1" <?php if($month==1) echo"selected"?>>January</option>
					<option value="2" <?php if($month==2) echo"selected"?>>February</option>
					<option value="3" <?php if($month==3) echo"selected"?>>March</option>
					<option value="4" <?php if($month==4) echo"selected"?>>April</option>
					<option value="5" <?php if($month==5) echo"selected"?>>May</option>
					<option value="6" <?php if($month==6) echo"selected"?>>June</option>
					<option value="7" <?php if($month==7) echo"selected"?>>July</option>
					<option value="8" <?php if($month==8) echo"selected"?>>August</option>
					<option value="9" <?php if($month==9) echo"selected"?>>September</option>
					<option value="10" <?php if($month==10) echo"selected"?>>October</option>
					<option value="11" <?php if($month==11) echo"selected"?>>November</option>
					<option value="12" <?php if($month==12) echo"selected"?>>December</option>
				</select>
				</div>
			</div>
			<div class="form-group">
                <label for="year" class="col-sm-2 control-label">Year</label>
				<div class="col-sm-4">
					<select class="form-control" id="year" name="year">
						<option value="2015" <?php if($year==2015) echo"selected"?>>2015</option>
					</select>
				</div>
            </div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" name="submit" value="yes" class="btn btn-primary">Submit</button>
				</div>
			</div>
        </form>
        <?php
            if (isset($_GET["submit"]) && $_GET["submit"] == "yes" && 
                isset($_GET["month"]) && isset($_GET["year"])) {
                echo "<h2 style='text-align:center;'>";
                echo "Duty Money Tracking for $monthName[$month] $year";
                echo "</h2>";
            }
        ?>
        <div class='progressbar'>
        <?php
            if (isset($_GET["submit"]) && $_GET["submit"] == "yes" && 
                isset($_GET["month"]) && isset($_GET["year"])) {
                if (count($trackingData) > 0) {
                    echo "<ol class='selected-step-".$currentStep."'>";
                    for ($i = 0; $i < count($trackingData); ++$i) {
                        $row = $trackingData[$i];
                        echo "<li class='step-".($i+1)."'>";
                        echo $row['progress'];
                        echo "<br/>Treasurer ";
                        if ($row['treasurer'] == 0) {
                            if ($trackingStatus == 'treasurer') {
                                $params = array_merge($_GET, array("switch" => "1", "indexNo" => $row['indexNo']));
                                $new_query_string = http_build_query($params);
                                echo "<a href='tracking?$new_query_string'>";
                            }
                            echo $HTML_NO;
                            if ($trackingStatus == 'treasurer') {
                                echo "</a>";
                            }
                        } else if ($row['treasurer'] == 1) {
                            if ($trackingStatus == 'treasurer') {
                                $params = array_merge($_GET, array("switch" => "0", "indexNo" => $row['indexNo']));
                                $new_query_string = http_build_query($params);
                                echo "<a href='tracking?$new_query_string'>";
                            }
                            echo $HTML_YES;
                            if ($trackingStatus == 'treasurer') {
                                echo "</a>";
                            }
                        } else {
                            echo "-";
                        }
                        echo "<br/>Comcen ";
                        if ($row['comcen'] == 0) {
                            if ($trackingStatus == 'comcen') {
                                $params = array_merge($_GET, array("switch" => "1", "indexNo" => $row['indexNo']));
                                $new_query_string = http_build_query($params);
                                echo "<a href='tracking?$new_query_string'>";
                            }
                            echo $HTML_NO;
                            if ($trackingStatus == 'comcen') {
                                echo "</a>";
                            }
                        } else if ($row['comcen'] == 1) {
                            if ($trackingStatus == 'comcen') {
                                $params = array_merge($_GET, array("switch" => "0", "indexNo" => $row['indexNo']));
                                $new_query_string = http_build_query($params);
                                echo "<a href='tracking?$new_query_string'>";
                            }
                            echo $HTML_YES;
                            if ($trackingStatus == 'comcen') {
                                echo "</a>";
                            }
                        } else {
                            echo "-";
                        }
                        echo "</li>";
                    }
                    echo "<li class='step-".(count($trackingData)+1)."'>Done</li>";
                    echo "</ol>";
                } else {
                    if ($trackingStatus == 'treasurer' || $trackingStatus == 'comcen') {
                        $params = array_merge($_GET, array("create" => "yes"));
                        $new_query_string = http_build_query($params);
                        echo "<a href='tracking?$new_query_string' class='btn btn-primary'>"
                                . "Create New for $monthName[$month] $year</a>";
                    }
                }
            }
        ?>
		</div>
        </div>
		<?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>