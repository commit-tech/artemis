<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }   
?>
<?php include_once(dirname(__FILE__).'/Controller/ScheduleController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php
    $scheduleController = ScheduleController::getInstance();
    $userController = UserController::getInstance();
    $dutyController = DutyController::getInstance();
    $userID = $_SESSION['user_id'];
    if (isset($_POST['clearall'])) {
        $scheduleController->clearAllAvailability();
    }
    if ($userController->isAdmin($userID) == 0) {
        header("Location: index");
        exit;
    }
    $dayList = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
    $day = "Monday";
    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
?>

<html>
    <head>
        <title>NUSSU commIT</title>
        <link href="includes/css/bootstrap.min.css" rel="stylesheet">
        <link href="includes/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="includes/css/style.css" rel="stylesheet">
	<link href="includes/img/logo.ico" rel="icon">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php $page = "scheduleavailability"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
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
            <div class="row">
                <div class="col-sm-7">
                    <h1>Admin Panel Schedule Availability</h1>
                </div>
                <div class="col-sm-5 well">
                    <a class="btn btn-default" href="scheduleavailability">User Panel</a>
                    <a class="btn btn-danger" onclick=clearAvail()>Clear Availability</a>
                </div>
            </div>
        <div class="row">
        <form action="processscheduleavailability" method="post">
            <p align="center">
            <label for="assign_to">See availability for user (only those who are active only)</label>
            <select id="assign_to" name="set_user">
                <?php
                    $allUsers = $userController->getAllActiveUser();
                    usort($allUsers, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                    echo "<option value='unset'>Choose wisely</option>";
                    for ($i = 0; $i < count($allUsers); ++$i) {
                        echo "<option value='".$allUsers[$i]['id']."'>".$allUsers[$i]['name']."</option>";
                    }
                ?>
            </select>
            <input type='submit' class='btn btn-primary'/>
            </p>
        </form>
        <?php
            if (isset($_POST['set_user']) && $_POST['set_user'] != 'unset') {
                echo "<div align=\"center\">";
                echo "<h3 style='display:inline;'>Checking availability for ";
                echo "<h2 style='display:inline;'>".$userController->getUserName($_POST['set_user'])."</h2>";
                echo "</h3>";
                echo "</div>";
        ?>
            <table border=1 class="table edittable">
                <tr class='table_header'>
                    <td style="width: 6%">Date</td>
                    <?php
                    
                    for ($i = 0; $i < count($dutySchedule); ++$i)
                    {
                        echo "<th class=\"breakword timeslot\">".$dutySchedule[$i]["time"]."</th>";
                    }
                    ?>
                </tr>

                <?php
                function printTable($userID) {
                    $blackIDs = [103, 104, 105, 115, 116, 117, 118, 119];
                    global $userController;
                    global $dutySchedule;
                    global $day;
                    global $scheduleController;

                    for ($j = 0; $j < count($dutySchedule); ++$j)
                    {
                        $dutyID = $dutySchedule[$j]['id'];
                        if (in_array($dutyID,$blackIDs)) {
                            echo "<td class='black_cell'></td>";
                        } else {

                            $user = new User($userID, null, null, null, null, null, null, null);
                            $duty = new Duty($dutyID, null, null, null, null);
                            $availability = $scheduleController->getAvailability($user, $duty);

                            echo "<td class='duty_cell";
                            if ($availability == "AVAILABLE") {
                                echo " available_cell' ";
                            } else if ($availability == "NOT_AVAILABLE") {
                                echo " not_available_cell' ";
                            } else if ($availability == "UNSET") {
                                echo "' ";
                            }
                            echo ">";
                            if ($availability == "NOT_AVAILABLE") {
                                $availability = "NO";
                            } else if ($availability == "AVAILABLE") {
                                $availability = "YES";
                            }
                            echo "<p> $availability </p>";
                            echo "</td>";
                        }
                    }
                }


                for ($i = 0; $i < 7; ++$i)
                {
                    echo "<tr class='blank_row'/>";
                    $day = $dayList[$i];
                    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
                    $cellClass = ($i % 2 == 0 ? "yellow_cell" : "white_cell");
                    echo "<tr class=$cellClass>\n";
                        echo "<th class=\"breakword\">".$day."</th>";
                        printTable($_POST['set_user']);
                    echo "</tr>\n";
                }
                ?>
            </table>
        <?php 
            } else { ?>
                <table border=1 class="table edittable">
                <tr class='table_header'>
                    <td style="width: 6%">Date</td>
                    <?php
                    
                    for ($i = 0; $i < count($dutySchedule); ++$i)
                    {
                        echo "<th class=\"breakword timeslot\">".$dutySchedule[$i]["time"]."</th>";
                    }
                    ?>
                </tr>

                <?php
                function printTable() {
                    $blackIDs = [103, 104, 105, 115, 116, 117, 118, 119];
                    global $userController;
                    global $dutySchedule;
                    global $day;
                    global $scheduleController;

                    for ($j = 0; $j < count($dutySchedule); ++$j)
                    {
                        $dutyID = $dutySchedule[$j]['id'];
                        if (in_array($dutyID,$blackIDs)) {
                            echo "<td class='black_cell'></td>";
                        } else {

                            //$user = new User($userID, null, null, null, null, null, null, null);
                            $duty = new Duty($dutyID, null, null, null, null);
                            $usersAvailable = $scheduleController->getAvailabilityDutyId($duty);

                            echo "<td>";
                            //echo "<td><select>";
                            foreach($usersAvailable as $user) {
                                $name = $userController->getUserName($user['supervisor_id']);
                                if ($userController->isMC($user['supervisor_id'])) {
                                    echo "<b>".$name."</b><br/>";
                                } else {
                                    echo $name."<br/>";
                                }
                                //echo "<option>".$name."</option>";
                            }
                            //echo "</select></td>";
                            echo "</td>";
                        }
                    }
                }


                for ($i = 0; $i < 7; ++$i)
                {
                    echo "<tr class='blank_row'/>";
                    $day = $dayList[$i];
                    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
                    $cellClass = ($i % 2 == 0 ? "yellow_cell" : "white_cell");
                    echo "<tr class=$cellClass>\n";
                    echo "<th class=\"breakword\">".$day."</th>";
                    printTable();
                    echo "</tr>\n";
                }
                ?>
            </table>
            <?php
            }
        ?>
        </div>
        </div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
        <script>
            function clearAvail() {
                if (confirm("Are you sure to remove all availability of all users and change it to UNSET? This button should only be pressed at the beginning of a semester")) {
                    $.ajax({
                        type:'POST',
                        url:'processscheduleavailability',
                        data:'clearall=true',
                        success:function() {
                            location.reload();
                        }
                    });
                }
            }
        </script>
  </body>
</html>
