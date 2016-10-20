<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }	
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Controller/ProblemController.php');
    $userController = UserController::getInstance();
    $problemController = ProblemController::getInstance();
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
    if (isset($_POST['submit'])) {
        $newReport = array('reporter_id' => $_SESSION['user_id'],
                           'venue' => $_POST['venue'],
                           'description' => $_POST['description'],
                           'pc_number' => $_POST['pc_number'],
                           'critical' => $_POST['critical']);
        $problemController->addReport($newReport);
        header("Location: reportlist");
        exit;
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
        <?php $page = "report"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <a class='btn btn-default' href='reportlist'>Computer Problem Report List</a>
            <h4>
                IMPORTANT : READ THIS FIRST BEFORE REPORTING<br/>
                Look at "Computer Problem Report List" first and check whether the problem has been reported before<br/>
                If the problem is a connection problem (e.g. can't login, can't access internet), please try to unplug and plug
                back the Ethernet (LAN) cable on the computer. <br/>
                If that still doesn't fix the problem, try to restart the computer. <br/>
                Only report if that doesn't fix the problem.<br/>
                All reports will be mailed to Technical Cell. Please don't spam us.
            </h4>
            <form class="form-horizontal well" action="report" method="post" id="report_form">
                <div class="form-group">
                    <label class="control-label col-xs-2">Name</label>
                    <div class="col-xs-10">
                    <?php echo $_SESSION['user_name']; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="venue" class="control-label col-sm-2">
                    Venue
                    </label>
                    <div class="btn-group col-sm-4" data-toggle="buttons">
                        <label class="btn btn-default"><input type="radio" name="venue" value="yih" required />YIH</label>
                        <label class="btn btn-default"><input type="radio" name="venue" value="cl" />CL</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">
                    PC Number
                    </label>
                    <input type="text" name="pc_number"/>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">
                    Description
                    </label>
                    <textarea form='report_form' name='description' rows='10' cols='100' placeholder='Please try to describe the problem as descriptive as possible. If there is an error message, try to type the error message.'></textarea>
                </div>

                <div class="form-group">
                    <label for="venue" class="control-label col-sm-2">
                    Critical
                    </label>
                    <div class="btn-group col-sm-2" data-toggle="buttons">
                        <label class="btn btn-default"><input type="radio" name="critical" value="1" required />&#10004</label>
                        <label class="btn btn-default"><input type="radio" name="critical" value="0" />&#10008</label>
                    </div>
                    If the computer is completely broken and cannot be used (e.g. cannot login, cannot boot to Windows, etc.), choose the tick mark.
                    Otherwise, if the computer still can be used (e.g. noisy CPU, very slow, etc.), choose the cross mark.
                </div>
                
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div> 
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>
