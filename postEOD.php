<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Controller/AnnouncementController.php');
    include_once(dirname(__FILE__).'/Utils/Mailer.php');

    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }
    $userController = UserController::getInstance();
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
    $announcement_controller = AnnouncementController::getInstance();

    if(isset($_POST['submit'])){
        date_default_timezone_set('Asia/Singapore');
        $now = time();
        $now_str = date('l jS M Y H:i a', $now);
        $str = "";
        $title = "EOD @ ".$_POST['venue']." ".$now_str."";
        $str .= "Last person on duty: ".$_SESSION['user_name']."\n";
        if ($_POST['venue'] == "YIH") {
            $str .= "Paper: ".$_POST['paperbox']." boxes + ".$_POST['paperreams']." reams\n";
        } else if ($_POST['venue'] == "CL") {
            $str .= "L:".$_POST['paperleft']."%.\nM:".$_POST['papermiddle']."%.\nR:".$_POST['paperright']."%\n";
        }
        $str .= "Cartridge: ".$_POST['cartridge']."\n";
        if ($_POST['venue'] == "YIH") {
            $str .= "Reprint Ez-link value: ".$_POST['ezlink']."\nRemarks: ";
        } else if ($_POST['venue'] == "CL") {
            $str .= "Cashbox: ".$_POST['cashbox']."\nRemarks: ";
        }
        $str .= (trim($_POST['content'])) == "" ? "n/a" : $_POST['content']."\n";
        if($announcement_controller->addAnnouncement(array('title'=>$title,
                'content'=>$str,'time'=>$now_str,'timeline'=>$now))){
            $_SESSION['success'] = "EOD is successfully posted.";

            $notifyTargets = $userController->getEODTargets();
            sendMail($title, $str, $notifyTargets);

            header('Location: index');
            exit;
        }else{
            $_SESSION['error'] = "Fail to make EOD.";
            header('Location: postEOD');
            exit;
        }
        $_SESSION['error'] = $str;
        header('Location: postEOD');
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

        <?php $page = "postEOD"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }

        ?>
		<div class="container">
        <h1> Post EOD Form</h1>
        <br/>
        <form class="form-horizontal well" action="postEOD" method="post">
            <?php
            if (!isset($_POST['venue']))
            {?>
                <label class = "col-sm-2 control-label">Venue: </label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default"><input type="radio" name="venue" value="YIH" required />YIH</label>
                    <label class="btn btn-default"><input type="radio" name="venue" value="CL" />CL</label>
                </div>
                <div class="btn-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="submitvenue" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            <?php
            } else {
                if ($_POST['venue'] == 'YIH') {?>
                    <div class ="form-group">
                        <label class = "col-sm-2 control-label">Paper: </label>
                        <div class ="col-sm-1">
                            <input type="text" name = "paperbox" class="form-control" placeholder = "boxes" required>
                        </div>
                        <div class ="col-sm-1">
                            <input type="text" name = "paperreams" class="form-control" placeholder = "reams" required>
                        </div>
                    </div>
                <?php
                } else if ($_POST['venue'] == 'CL') { ?>
                    <div class ="form-group">
                        <label for="paperleft" class = "col-sm-2 control-label">Cupboard L: </label>
                        <div class ="col-sm-1">
                            <input type="text" name = "paperleft" id="paperleft" class="form-control" value = "" required>
                        </div>
                        %
                    </div>
                    <div class ="form-group">
                        <label for ="papermiddle" class = "col-sm-2 control-label">Cupboard M: </label>
                        <div class ="col-sm-1">
                            <input type="text" name = "papermiddle" id="papermiddle" class="form-control" value = "" required>
                        </div>
                        %
                    </div>
                    <div class ="form-group">
                        <label for="paperright" class = "col-sm-2 control-label">Cupboard R: </label>
                        <div class ="col-sm-1">
                            <input type="text" name = "paperright" id="paperright" class="form-control" value = "" required>
                        </div>
                        %
                    </div>
                <?php
                } ?>

                <div class = "form-group">
                    <label for="cartridge" class="col-sm-2 control-label">Cartridge: </label>
                    <div class = "col-sm-4">
                        <input type = "text" id="cartridge" name = "cartridge" class="form-control" required>
                    </div>
                </div>

                <?php
                if ($_POST['venue'] == 'YIH') {?>
                    <div class = "form-group">
                    <label for="ezlink" class = "col-sm-2 control-label">Reprint EZ-Link: </label>
                    <div class = "col-sm-4">
                        <input type = "text" id="ezlink" name = "ezlink" class = "form-control" required>
                    </div>
                </div>
                <?php
                } else if ($_POST['venue'] == 'CL') { ?>
                <div class = "form-group">
                    <label for="ezlink" class = "col-sm-2 control-label">Cashbox: </label>
                    <div class = "col-sm-4">
                        <input type = "text" id="cashbox" name = "cashbox" class = "form-control" required>
                    </div>
                </div>
                <?php
                } ?>

                <div class="form-group">
                    <label for="remarks" class = "col-sm-2 control-label">Remarks: </label>
                    <div class="col-sm-4">
                        <textarea style="resize:none" id="remarks" name="content" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <input type="hidden" name="venue" value="<?php echo $_POST['venue'] ?>" />
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            <?php
            } ?>
        </form>
        </div>

        <?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>
