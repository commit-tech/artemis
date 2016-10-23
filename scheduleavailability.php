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
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
    if (isset($_POST['assign_to'])) {
        for ($i = 1; $i <= 119; ++$i) {
            if (isset($_POST["set_availability_".$i]) && $_POST["set_availability_".$i] != 'unset') {
                $user = new User($userID, null, null, null, null, null, null, null);
                $duty = new Duty($i, null, null, null, null);
                $availability = $_POST["set_availability_".$i];
                $scheduleController->setAvailability($user, $duty, $availability);
            }
        }
        header("Location: scheduleavailability");
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
                    <h1>Enter Schedule Availability</h1>
                </div>
                <div class="col-sm-5 well">
                    <?php
                    if ($userController->isAdmin($userID)) {
                        echo "<a class=\"btn btn-default\" href=\"processscheduleavailability\">Admin Panel</a>";
                    }
                    ?>
                    <button class="btn btn-default" onclick="clearAll()">Clear Selection</button>
                </div>
            </div>
        <div class="row">
        <form action="scheduleavailability" method="post">
            <p align="center">
            <label for="assign_to">Set the selected cells to:</label>
            <select id="assign_to" name="assign_to" onchange=change()>
                <?php
                    echo "<option value='unset'>Choose wisely</option>";
                    echo "<option value='AVAILABLE' >YES</option>";
                    echo "<option value='NOT_AVAILABLE' >NO</option>";
                    echo "<option value='UNSET' >UNSET</option>";
                ?>
            </select>
            <input type='submit' class='btn btn-primary'/>
            </p>
            <p align="center">Orange cells are pending changes. Click the blue submit button to save pending changes</p>
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
                    global $userID;
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

                            $onclickFunction = "\"cellClickHandler(" . $dutyID . ", '" . $day ."')\"";
                            $onmouseoverFunction = "\"cellMouseoverHandler(" . $dutyID . ", '" . $day ."')\"";
                            $id = "cell_" . $dutyID . "_" . $day;
                            $assignto = "set_availability_" . $dutyID;

                            echo "<td class='duty_cell";
                            if ($availability == "AVAILABLE") {
                                echo " available_cell' ";
                            } else if ($availability == "NOT_AVAILABLE") {
                                echo " not_available_cell' ";
                            } else if ($availability == "UNSET") {
                                echo "' ";
                            }
                            //echo "id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            echo "id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            if ($availability == "NOT_AVAILABLE") {
                                $availability = "NO";
                            } else if ($availability == "AVAILABLE") {
                                $availability = "YES";
                            }
                            echo "<p> $availability </p>";
                            echo "<input type='hidden' name='$assignto' id='$assignto' value='unset'/>";
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
        </form>
        </div>
        </div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
  </body>
  <script>
    var currentSelection = null;
    var selections = {};
    var selected_cell = {};

    function clearAll() {
        $('.duty_cell').each(function() {
            var dutyDay = $(this).attr('id').split('_')[2];
            var dutyID = $(this).attr('id').split('_')[1];
            $(this).removeClass('selected_cell');
            selected_cell[dutyID] = false;
        });
    }

    function clearSelection(day) {
        $('.duty_cell').each(function() {
            var dutyDay = $(this).attr('id').split('_')[2];
            var dutyID = $(this).attr('id').split('_')[1];
            if (dutyDay === day) {
                $(this).removeClass('selected_cell');
                selected_cell[dutyID] = false;
            }
        });
    }

    function select(dutyId, dutyDay) {
      var name = dutyId;
      $('input[name=' + name + ']').prop('checked', true);
      $('#cell_' + name + '_' + dutyDay).addClass('selected_cell');
      selected_cell[name] = true;
    }

    function getCell(dutyId) {
      var cellId = "cell_" + dutyId;
      return $('#' + cellId);
    }

    function startSelection(dutyId, dutyDay) {
      clearSelection(dutyDay);
      selections[dutyDay] = [];
      selections[dutyDay][0] = dutyId;
      currentSelection = dutyDay;
      select(dutyId, dutyDay);
    }

    function endSelection(dutyId, dutyDay) {
      var selectionStart = selections[currentSelection][0];
      var startLoop = Math.min(selectionStart, dutyId);
      var endLoop = Math.max(selectionStart, dutyId);
      clearSelection(dutyDay);
      for (var i = startLoop; i <= endLoop; i++) {
        select(i, dutyDay);
      }
      currentSelection = null;
    }

    function cellClickHandler(dutyId, dutyDay) {
      if (currentSelection === null) {
        startSelection(dutyId, dutyDay);
      } else {
        if (currentSelection === dutyDay) {
          endSelection(dutyId, dutyDay);
        }
      }
    }
    function cellMouseoverHandler(dutyId, dutyDay) {
      if (currentSelection === dutyDay) {
        var selectionStart = selections[currentSelection][0];
        var startLoop = Math.min(selectionStart, dutyId);
        var endLoop = Math.max(selectionStart, dutyId);
        clearSelection(dutyDay);
        for (var i = startLoop; i <= endLoop; i++) {
          select(i, dutyDay);
        }
      }
    }

    function change() {
        var statusValue = $("#assign_to").val();
        var statusText = $("#assign_to option:selected").text();
        $("#assign_to").val('unset');
        $('.duty_cell').each(function() {
            var dutyDay = $(this).attr('id').split('_')[2];
            var dutyID = $(this).attr('id').split('_')[1];
            var assignto = "set_availability_" + dutyID;
            if (dutyID in selected_cell && selected_cell[dutyID]) {
                $(this).addClass("pending_schedule_cell");
                $($(this).children("p")).html(statusText);
                $("#" + assignto).attr("value",statusValue);
            }
        });
        clearAll();
    }

  </script>

</html>
