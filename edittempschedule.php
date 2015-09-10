<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
	}	
        
?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
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
    if (isset($_POST['assign_to'])) {
        for ($i = 1; $i <= 119; ++$i) {
            foreach (['yih','cl'] as $j) {
                if ($_POST["assignto_".$j."_".$i] != 'unset') {
                    $day = Date::stringToDay($_POST["date_".$j."_".$i]);
                    $newUser = new User($_POST["assignto_".$j."_".$i], null, null, null, null, null, null, null);
                    $duty = new DailyDuty($i, $day->getDay(), null, null, $j, $day->getDate(), $day->getMonth(), $day->getYear());
                    $dutyController->assignTemporaryDuty($newUser,$duty);
                }
            }
        }
        header("Location: index");
        exit;
    }
    $day = Date::getToday();
    $plus = 0;
    if (isset($_GET["plus"])) {
        $plus = $_GET["plus"];
    }
    $day = $day->addDay($plus);
    $dutySchedule = $dutyController->getDutySchedule($day->getDate(),$day->getMonth(),$day->getYear());
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
                    <h1>Edit Temporary Schedule</h1>
                </div>
                <div class="col-sm-5 well">
                    <a class="btn btn-default" href="editschedule">Edit Permanent</a> 
                    <button class="btn btn-default" onclick="clearAll()">Clear Selection</button>
                    <a class="btn btn-default" href="?plus=<?php echo $plus-7; ?>">Previous Week</a>
                    <a class="btn btn-default" href="?plus=<?php echo $plus+7; ?>">Next Week</a>
                </div>
            </div>
        <div class="row">
        <form action="edittempschedule" method="post">
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
                    for ($i = 0; $i < count($dutySchedule); ++$i) {
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

                        for ($j = 0; $j < count($dutySchedule); ++$j)
                        {   
                            $supervisorID = $dutySchedule[$j]["supervisor_".$location];
                            $name = "";
                            if ($supervisorID < 0) {
                                $name = $userController->getUserName($supervisorID * (-1));
                            } else if ($supervisorID >= 0) {
                                $name = $userController->getUserName($supervisorID);
                            }
                            $dutyID = $dutySchedule[$j]["id"];
                            $onclickFunction = "\"cellClickHandler('" . $location . "', " . $dutyID . ", '" . $day->dayToString() ."')\"";
                            $onmouseoverFunction = "\"cellMouseoverHandler('" . $location . "', " . $dutyID . ", '" . $day->dayToString() ."')\"";
                            $id = "cell_" . $location . "_" . $dutyID . "_" . $day->dayToString();
                            $assignto = "assignto_" . $location . "_" . $dutyID;
                            $date = "date_" . $location . "_" . $dutyID;
                            $dateFormat = $day->dayToString();

                            echo "<td class='duty_cell' ";
                            if ($name == "Drop") {
                                echo "class='dropped_cell' ";
                            } else if ($name == "NO_DUTY") {
                                echo "class='noduty_cell' ";
                            }
                            echo "id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            echo "<p> $name </p>";
                            echo "<input type='hidden' name='$assignto' id='$assignto' value='unset'/>";
                            echo "<input type='hidden' name='$date' id='$date' value='$dateFormat'/>";
                            echo "</td>";




                            /*$id = "cell_" . $location . "_" . $dutyID  . "_" . $day->dayToString();
                            if ($supervisorID < 0) {
                                echo "<td class='dropped_cell' id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            } else if ($name == "NO_DUTY") {
                                echo "<td class='noduty_cell' id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            } else {
                                echo "<td id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            }
                            echo $name;
                            $dateFormat = $day->dayToString();
                            echo "<input type='checkbox' name='".$location."_".$dutyID."' value='".$dateFormat."' style='display:none' />";
                            echo "</td>";*/
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
                    echo "<tr class=$cellClass>\n";
                        echo "<th rowspan=2 class=\"breakword\">".substr($day->getDay(),0,3)."<br>".$day->printToString()."</th>";
                        echo "<th>YIH</th>";
                        printTable("yih");
                    echo "</tr>\n";
                    echo "<tr class=$cellClass>\n";
                        echo "<th>CL</th>";
                        printTable("cl");
                    echo "</tr>\n";

                    $day = $day->addDay(1);
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
