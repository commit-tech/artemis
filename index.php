<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
	}	
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    $userController = UserController::getInstance();
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
?>


<html>
	<head>
		<title>NUSSU commIT</title>
		<link href="includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="includes/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="includes/css/style.css" rel="stylesheet">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
	</head>
    <body>
        <?php $page = "home"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php include(dirname(__FILE__).'/Controller/DutyController.php');?>
		<div class="container">
        <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            $dutyController = DutyController::getInstance();
            $userID = $_SESSION['user_id'];
            $day = Date::getToday();
            $plus = 0;
            if (isset($_GET["plus"])) {
                $plus = $_GET["plus"];
            }
            $day = $day->addDay($plus);
            $dutySchedule = $dutyController->getDutySchedule($day->getDate(),$day->getMonth(),$day->getYear());
        ?>
		<div class="row">
        <?php
        if ($userController->isAdmin($userID) == 1) { ?>
		<div class="col-sm-7">
        <h1>Duty Timetable for the Week</h1>
		</div>
		<div class="col-sm-5 well">
            <a class="btn btn-default" href="editschedule">Edit Permanent</a>
            <a class="btn btn-default" href="edittempschedule">Edit Temporary</a>
			<a class="btn btn-default" href="?plus=<?php echo $plus-7; ?>">Previous Week</a>
			<a class="btn btn-default" href="?plus=<?php echo $plus+7; ?>">Next Week</a>
		</div>
            <?php
        }else{?>
		<div class="col-sm-9">
        <h1>Duty Timetable for the Week</h1>
		</div>
		<div class="col-sm-3 well">
			<a class="btn btn-default" href="?plus=<?php echo $plus-7; ?>">Previous Week</a>
			<a class="btn btn-default" href="?plus=<?php echo $plus+7; ?>">Next Week</a>
		</div>
		<?php
		}
        ?>
		
		</div>

        <div class="row">
		
        <table border=1 class="table">
            <tr class="table_header">
                <td style="width: 6%">Date</td>
                <td style="width: 4.8%">Venue</td>
                <?php
                echo "Your duty hours this week : ";
                echo $dutyController->countDutyHours($_SESSION['user_id'],$day);
                echo "/";
                if ($userController->isMC($userID) == 1) {
                    echo "∞";
                } else {
                    echo "16";
                }
                echo " hours";
                for ($i = 0; $i < count($dutySchedule); ++$i)
                {
                    echo "<th class=\"breakword timeslot\">".$dutySchedule[$i]["time"]."</th>";
                }
                ?>
            </tr>
            
            <?php
            function printTable($location) {
                global $i;
                global $day;
                global $userController;
                global $dutySchedule;
                
                    $columnLength = 0;
                    for ($j = 0; $j <= count($dutySchedule); ++$j)
                    {
                        //echo $userController->getUserName($dutySchedule[$j-1]["supervisor_".$location]);
                        //continue;
                        if ($j == count($dutySchedule) || ($j > 0 && $dutySchedule[$j]["supervisor_".$location] != $dutySchedule[$j-1]["supervisor_".$location])) {
                            if ($dutySchedule[$j-1]["supervisor_".$location] == 0)
                                $name = "";
                            else if ($dutySchedule[$j-1]["supervisor_".$location] < 0)
                                $name = $userController->getUserName($dutySchedule[$j-1]["supervisor_".$location] * (-1));
                            else
                                $name = $userController->getUserName($dutySchedule[$j-1]["supervisor_".$location]);
                            if ($dutySchedule[$j-1]["supervisor_".$location] < 0) {
                                echo "<td colspan=".$columnLength." class='dropped_cell'>";
                            } else if ($_SESSION['user_id'] == $dutySchedule[$j-1]["supervisor_".$location]) {
                                echo "<td colspan=".$columnLength." class='selfduty_cell'>";
                            } else if ($name == "") {
                                echo "<td colspan=".$columnLength." class='noduty_cell'>";
                            } else {
                                echo "<td colspan=".$columnLength.">";
                            }
                            $scheduleID = $dutySchedule[$j-1]['id'];
                            if ($_SESSION['user_id'] == $dutySchedule[$j-1]["supervisor_".$location]) {
                                echo "<a href='releaseduty.php?date=".$day->getDate()."&month=".$day->getMonth()."&year=".$day->getYear()."&venue=".$location."&schedule_id=".($scheduleID-$columnLength+1).",".($scheduleID)."'>";
                            } else if ($dutySchedule[$j-1]["supervisor_".$location] < 0) {
                                echo "<a href='grabduty.php?date=".$day->getDate()."&month=".$day->getMonth()."&year=".$day->getYear()."&venue=".$location."&schedule_id=".($scheduleID-$columnLength+1).",".($scheduleID)."'>";
                            } else {
                                echo "<a href='members?name=$name'>";
                            }
                            echo $name;
                            if ($_SESSION['user_id'] == $dutySchedule[$j-1]["supervisor_".$location]) {
                                echo "</a>";
                            } else if ($dutySchedule[$j-1]["supervisor_".$location] < 0) {
                                echo "</a>";
                            } else {
                                echo "</a>";
                            }
                            echo "</td>";
                            $columnLength = 1;
                        } else {
                            ++$columnLength;
                        }
                    }
            }
            
            while ($day->getDay() != "Monday") {
                    $day = $day->minusDay(1);
                }
            
            for ($i = 0; $i < 7; ++$i)
            {
                echo "<tr class='blank_row'/>";
                $dutySchedule = $dutyController->getDutySchedule($day->getDate(),$day->getMonth(),$day->getYear());
                $cellClass = ($i % 2 == 0 ? "yellow_cell" : "white_cell");
                echo "<tr class=\"$cellClass\">\n";
                    echo "<th rowspan=2 class=\"breakword\">".substr($day->getDay(),0,3)."<br>".$day->printToString()."</th>";
                    echo "<th>YIH</th>";
                    printTable("yih");
                echo "</tr>\n";
                echo "<tr class=$cellClass>\n";
                    echo "<th>AS8</th>";
                    printTable("cl");
                echo "</tr>\n";
                
                $day = $day->addDay(1);
            }
            ?>
        </table>
		
		</div>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
