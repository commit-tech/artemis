<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;        
    }
    if(!isset($_GET['date']) || !isset($_GET['month']) || !isset($_GET['year']) || !isset($_GET['venue']) || !isset($_GET['schedule_id'])){
        if (!isset($_POST['drop'])) {
            header("Location: index");
            exit;
        }
    }
?>
<?php include_once(dirname(__FILE__).'/Model/Date.php');?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php include_once(dirname(__FILE__).'/Utils/Mailer.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
    if (isset($_POST['drop']) && $_POST['drop'] == 'yes') {
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
                if ($dutyController->getSupervisorID($dailyDuty) != $user_id) {
                    header("Location: index");
                    exit;
                }
                $dutyController->releaseDuty($user,$dailyDuty);
            }
        }

        $startTime = $dutyController->getTimeDuty($startID);
        $endTime = $dutyController->getTimeDuty($endID);
        $dutyTime = substr($startTime,0,5)."-".substr($endTime,6,5);
        
        $monthName = ["","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        $monthName = $monthName[intval($month)];
        
        $subject = "Duty notification: {$dutyTime} on {$day}, {$date} {$monthName} {$year} at {$venue}";
        $message = "From: {$user_name}\nDate:{$day} {$date} {$monthName} {$year}\n";
        $message.= "Duty for grab at times: {$dutyTime}\n";
        $message.= "Venue:".strtoupper($venue)."\n";
        $message.= "\n\nGo to ".DOMAIN."/grablist to grab it!";
        $message.= FOOTER_MESSAGE;
        
        //$subject = "gw ganteng";
        //$message = "banget";

        $notifyTargets = $userController->getNotifyUsers();
        sendMail($subject, $message, $notifyTargets);

        header("Location: index");
        exit;
    }
    $date = $_GET['date'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $venue = $_GET['venue'];
    $scheduleID = explode(",",$_GET['schedule_id']);
    $startID = $scheduleID[0];
    $endID = $scheduleID[1];
    $user_id = $_SESSION['user_id'];
    $user_name= $_SESSION['user_name'];
    for ($i = $startID; $i <= $endID; ++$i) {
        $dailyDuty = new DailyDuty($i, null, null, null, $venue, $date, $month, $year);
        if ($dutyController->getSupervisorID($dailyDuty) != $user_id) {
            header("Location: index");
            exit;
        }
    }
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
        <?php $page = "home"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
		<div class="container">
        <h1>Release Your Duty</h1>
		<dl class="dl-horizontal well">
        <dt>Your Name</dt><dd><?php echo $user_name; ?></dd>
        <dt>Venue</dt><dd><?php echo $venue; ?></dd>
        <dt>Date</dt><dd><?php echo (new Date($date,$month,$year))->printToString(); ?></dd>
        <dt>Timeslot To Drop</dt><dd>
        <form action="releaseduty.php" method="post">
            <input type="hidden" name="drop" value="yes">
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="hidden" name="month" value="<?php echo $month; ?>">
            <input type="hidden" name="year" value="<?php echo $year; ?>">
            <input type="hidden" name="venue" value="<?php echo $venue; ?>">
            <input type="hidden" name="start_id" value="<?php echo $startID; ?>">
            <input type="hidden" name="end_id" value="<?php echo $endID; ?>">
            <?php
            $indexID = 0;
            for ($i = $startID; $i <= $endID; ++$i) {
                echo "<div class='checkbox-inline'>";
                echo "<input type='checkbox' name='$i'/ checked>";
                echo $dutyController->getTimeDuty($i);
                echo "</div>";
                if (++$indexID % 3 == 0 || $i == $endID) {
                    echo "<br />";
                }
            }
            ?>
            <br /><button type='submit' name='submit' class='btn btn-primary'>Release</button>
        </form>
		</dd></dl>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
