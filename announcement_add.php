<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Controller/AnnouncementController.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }
    $announcement_controller = AnnouncementController::getInstance();

    if(isset($_POST['submit'])){
        $now = time();
        $now_str = date('Y-m-d', $now);
        if($announcement_controller->addAnnouncement(array('title'=>$_POST['title'],
                'content'=>htmlspecialchars($_POST['content']),'time'=>$now_str,'timeline'=>$now))){
            $_SESSION['success'] = "Announcement is successfully posted.";
            header('Location: announcement');
            exit;
        }else{
            $_SESSION['error'] = "Fail to make announcement.";
            header('Location: announcement');
            exit;
        }
    }

?>

<html>
	<head>
		<title>NUSSU commIT</title>
		<link href="includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="includes/css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="includes/img/logo.ico" rel="icon">
        <link href="includes/css/style.css" rel="stylesheet">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
	</head>
    <body>                     

        <?php $page = "announcement_add"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php 
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            } 
            
        ?>

        <h1> Add Announcement</h1>
        <br/>
        <form class="form-horizontal" action="announcement_add" method="post">
            <div class="form-group">
                <label class = "col-sm-2 control-label">Title: </label>
                <div class="col-sm-6">
                    <input type = "text" name="title" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Content: </label>
                <div class="col-sm-6">
                    <textarea style="resize:none" name="content" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
          </div>
        </form>
        

        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
