<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;        
    }
?>
<?php include_once(dirname(__FILE__).'/Model/Date.php');?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
    if (isset($_POST['grab']) && $_POST['grab'] == 'yes') {
        $startID = $_POST['start_id'];
        $endID = $_POST['end_id'];
        $date = $_POST['date'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $day = (new Date($date,$month,$year))->getDay();
        $venue = $_POST['venue'];
        $user_id = $_SESSION['user_id'];
        $user_name= $_SESSION['user_name'];
        $user = new User($user_id, null, null, null, null, null, null, null);
        for ($i = $startID; $i <= $endID; ++$i) {
            if (isset($_POST[$i]) && $_POST[$i] == 'on') {
                $dailyDuty = new DailyDuty($i, $day, null, null, $venue, $date, $month, $year);
                if ($dutyController->getSupervisorID($dailyDuty) >= 0) {
                    header("Location: index");
                    exit;
                }
                $dutyController->grabDuty($user,$dailyDuty);
            }
        }
        header("Location: index");
        exit;
    }
    $availableDuties = $dutyController->getAvailableDuties(null,null,null,null);
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
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="includes/js/bootstrap.min.js"></script>
	</head>
    <body>
	
        <?php $page = "grab"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
		<div class="container">
        <h1>Grab A Duty</h1>



        <?php
        if (count($availableDuties) > 0) {
        ?>
		
            
            
			<h3>Available for grab :</h3>
                <table class="table  table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Timeslots</th>
                        </tr>
                    </thead>
                <?php
                for ($i = 0; $i < count($availableDuties); ++$i) {
                    $date = $availableDuties[$i]["date"];
                    $month = $availableDuties[$i]["month"];
                    $year = $availableDuties[$i]["year"];
                    $venue = $availableDuties[$i]["venue"];
                    echo "<tr>";
                    echo "<td>".(new Date($date,$month,$year))->printToString()."</td>";
                    echo "<td>".$venue."</td>";

                    echo "<td>"; ?>
                    <script language="JavaScript">
                        function Click(i, k) {
                            for (var j = 0; j < k; ++j) {
                                if ($("#button" + i + "-" + j)[0].checked) {
                                    $("#button" + i + "-" + j)[0].checked = false;
                                } else {
                                    $("#button" + i + "-" + j)[0].checked = true;
                                }
                            }
                        }
                    </script>
                    <?php 
                    $scheduleID = $dutyController->getAvailableDuties($date,$month,$year,$venue);
                    echo "<button onclick=Click(".$i.",".count($scheduleID).") class='btn btn-default'> Toggle All</button><br />";
                    ?>
                    <form action='grablist.php' method='post'>

                        <input type="hidden" name="grab" value="yes">
                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                        <input type="hidden" name="month" value="<?php echo $month; ?>">
                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                        <input type="hidden" name="venue" value="<?php echo $venue; ?>">
                        <input type="hidden" name="start_id" value="1">
                        <input type="hidden" name="end_id" value="1000">


                        <?php
                        $indexID = 0;
                        for ($j = 0; $j < count($scheduleID); ++$j) { 
                            echo "<div class='checkbox-inline'>"; ?>
                            <input id='<?php echo "button$i-$j";?>' type="checkbox" name="<?php echo $scheduleID[$j]["schedule_id"];?>"> <?php
                            echo $dutyController->getTimeDuty($scheduleID[$j]["schedule_id"]);
                            echo "</div>";
                            if (++$indexID % 3 == 0 || $j == count($scheduleID) - 1) {
                                echo "<br />";
                            }
                        }
                        echo "<button type='submit' name='submit' class='btn btn-primary'>Grab</button>";
                    echo "</form>";

                    

                    echo "</td>";
                    echo "</tr>";
                }
                ?>

                </table>
            
        <?php
        } else { ?>
            <div class="col-md-10">
            No timeslot to grab
            </div> <?php
        }
        ?>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
