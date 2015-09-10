<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }	
?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/ScheduleController.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
    $scheduleController = ScheduleController::getInstance();
    $userID = $_SESSION['user_id'];
    if ($userController->isAdmin($userID) == 0) {
        header("Location: index");
		exit;
    }
    if(isset($_SESSION['error'])){
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if(isset($_POST['assign_to'])) {
        for ($i = 1; $i <= 119; ++$i) {
            foreach (['yih','cl'] as $j) {
                if ($_POST["assignto_".$j."_".$i] != 'unset') {
                    $newUser = new User($_POST["assignto_".$j."_".$i], null, null, null, null, null, null, null);
                    $duty = new Duty($i, null, null, null, $j);
                    $dutyController->assignPermanentDuty($newUser,$duty);
                }
            }
        }
        header("Location: index");
        exit;
    }
    $dayList = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
    $day = "Monday";
    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
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
        <?php $page = "home"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <h1>Edit Permanent Schedule</h1>
                </div>
                <div class="col-sm-5 well">
                    <a class="btn btn-default" href="edittempschedule">Edit Temporary</a> 
                    <button class="btn btn-default" onclick="clearAll()">Clear Selection</button>
                    <form style="display:inline-block;" method="post" action="automateschedule">
                        <input type="hidden" name="automate" value="yes"></input>
                        <button class="btn btn-default" href="">Automate Scheduling</button>
                    </form>
                </div>
            </div>
        <div class="row">
        <form action="editschedule" method="post">
            <p align="center">
            <label for="assign_to">Assign slot to:</label>
            <select id="assign_to" name="assign_to" onchange=change()>
                <?php
                    $allUsers = $userController->getAllUser();
                    usort($allUsers, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                    echo "<option value='unset'>Choose wisely</option>";
                    echo "<option value='0' >NO_DUTY</option>";
                    for ($i = 0; $i < count($allUsers); ++$i) {
                        echo "<option value='".$allUsers[$i]['id']."'>".$allUsers[$i]['name']."</option>";
                    }
                ?>
            </select>
            <input type='submit' class='btn btn-primary'/>
            </p>
            <table border=1 class="table edittable">
                <tr class='table_header'>
                    <td style="width: 6%">Date</td>
                    <td style="width: 4.8%">Venue</td>
                    <?php
                    
                    for ($i = 0; $i < count($dutySchedule); ++$i)
                    {
                        echo "<th class=\"breakword timeslot\">".$dutySchedule[$i]["time"]."</th>";
                    }
                    ?>
                </tr>

                <?php
                function printTable($location) {
                    global $userController;
                    global $dutySchedule;
                    global $day;

                        for ($j = 0; $j < count($dutySchedule); ++$j)
                        {
                            $name = $userController->getUserName($dutySchedule[$j]["supervisor_".$location]);
                            $dutyID = $dutySchedule[$j]["id"];
                            $onclickFunction = "\"cellClickHandler('" . $location . "', " . $dutyID . ", '" . $day ."')\"";
                            $onmouseoverFunction = "\"cellMouseoverHandler('" . $location . "', " . $dutyID . ", '" . $day ."')\"";
                            $id = "cell_" . $location . "_" . $dutyID . "_" . $day;
                            $assignto = "assignto_" . $location . "_" . $dutyID;
                            echo "<td class='duty_cell' ";
                            if ($name == "Drop") {
                                echo "class='dropped_cell' ";
                            } else if ($name == "NO_DUTY") {
                                echo "class='noduty_cell' ";
                            }
                            echo "id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            echo "<p> $name </p>";
                            echo "<input type='hidden' name='$assignto' id='$assignto' value='unset'/>";
                            echo "</td>";
                        }
                }


                for ($i = 0; $i < 7; ++$i)
                {
                    echo "<tr class='blank_row'/>";
                    $day = $dayList[$i];
                    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
                    $cellClass = ($i % 2 == 0 ? "yellow_cell" : "white_cell");
                    echo "<tr class=$cellClass>\n";
                        echo "<th rowspan=2 class=\"breakword\">".$day."</th>";
                        echo "<th>YIH</th>";
                        printTable("yih");
                    echo "</tr>\n";
                    echo "<tr class=$cellClass>\n";
                        echo "<th>CL</th>";
                        printTable("cl");
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
            var dutyDay = $(this).attr('id').split('_')[3];
            var dutyID = $(this).attr('id').split('_')[2];
            var dutyLoc = $(this).attr('id').split('_')[1];
            $(this).removeClass('selected_cell');
            selected_cell[dutyLoc + "_" + dutyID] = false;
        });
    }

    function clearSelection(location, day) {
        $('.duty_cell').each(function() {
            var dutyDay = $(this).attr('id').split('_')[3];
            var dutyID = $(this).attr('id').split('_')[2];
            var dutyLoc = $(this).attr('id').split('_')[1];
            if (dutyDay === day && dutyLoc == location) {
                $(this).removeClass('selected_cell');
                selected_cell[dutyLoc + "_" + dutyID] = false;
            }
        });
    }

    function select(dutyLocation, dutyId, dutyDay) {
      var name = dutyLocation + '_' + dutyId;
      $('input[name=' + name + ']').prop('checked', true);
      $('#cell_' + name + '_' + dutyDay).addClass('selected_cell');
      selected_cell[name] = true;
    }

    function getCell(dutyLocation, dutyId) {
      var cellId = "cell_" + dutyLocation + "_" + dutyId;
      return $('#' + cellId);
    }

    function startSelection(dutyLocation, dutyId, dutyDay) {
      clearSelection(dutyLocation, dutyDay);
      selections[dutyLocation + "_" + dutyDay] = [];
      selections[dutyLocation + "_" + dutyDay][0] = dutyId;
      currentSelection = dutyLocation + "_" + dutyDay;
      select(dutyLocation, dutyId, dutyDay);
    }

    function endSelection(dutyLocation, dutyId, dutyDay) {
      var selectionStart = selections[currentSelection][0];
      var startLoop = Math.min(selectionStart, dutyId);
      var endLoop = Math.max(selectionStart, dutyId);
      clearSelection(dutyLocation, dutyDay);
      for (var i = startLoop; i <= endLoop; i++) {
        select(dutyLocation, i, dutyDay);
      }
      currentSelection = null;
    }

    function cellClickHandler(dutyLocation, dutyId, dutyDay) {
      if (currentSelection === null) {
        startSelection(dutyLocation, dutyId, dutyDay);
      } else {
        if (currentSelection === dutyLocation + "_" + dutyDay) {
          endSelection(dutyLocation, dutyId, dutyDay);
        }
      }
    }
    function cellMouseoverHandler(dutyLocation, dutyId, dutyDay) {
      if (currentSelection === dutyLocation + "_" + dutyDay) {
        var selectionStart = selections[currentSelection][0];
        var startLoop = Math.min(selectionStart, dutyId);
        var endLoop = Math.max(selectionStart, dutyId);
        clearSelection(dutyLocation, dutyDay);
        for (var i = startLoop; i <= endLoop; i++) {
          select(dutyLocation, i, dutyDay);
        }
      }
    }

    function change() {
        var userID = $("#assign_to").val();
        var username = $("#assign_to option:selected").text();
        $("#assign_to").val('unset');
        $('.duty_cell').each(function() {
            var dutyDay = $(this).attr('id').split('_')[3];
            var dutyID = $(this).attr('id').split('_')[2];
            var dutyLoc = $(this).attr('id').split('_')[1];
            var assignto = "assignto" + "_" + dutyLoc + "_" + dutyID;
            var name = dutyLoc + "_" + dutyID;
            if (name in selected_cell && selected_cell[name]) {
                $(this).addClass("pending_cell");
                $($(this).children("p")).html(username);
                $("#" + assignto).attr("value",userID);
            }
        });
        clearAll();
    }

  </script>

</html>
