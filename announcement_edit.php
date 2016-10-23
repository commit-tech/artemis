<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Controller/AnnouncementController.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }
    $announcement_controller = AnnouncementController::getInstance();
    
    if(!isset($_GET['id'])){
        if(isset($_POST['submit'])){
            $id = $_POST['id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            if($announcement_controller->updateAnnouncement($id, array("title"=>$title, "content"=>$content))){
                $_SESSION['success'] = "Updated successfully.";
                header("Location:announcement");
                exit;
            }
            else{
                $_SESSION['error'] = "Update failed.";
                header("Location: announcement");
                exit;
            }
        }
        header("Location:announcement");
        exit;
    }

        
    $id = $_GET['id'];
    if(!$getAnnouncement = $announcement_controller->getAnnouncement($id)){
        $_SESSION['error'] = "Unable to update, announcement does not exist.";
        header("Location: announcement");
        exit;
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

        <?php $page = "announcement_edit"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php 
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            } 
            
        ?>

        <h1> Announcement editz</h1>
        <br/>
        <form class="form-horizontal" action="announcement_edit" method="post">
            <div class="form-group">
                <label class = "col-sm-2 control-label">Title: </label>
                <div class="col-sm-6">
                    <input type = "text" name="title" value = "<?php echo $getAnnouncement['title'] ?>" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Content: </label>
                <div class="col-sm-6">
                    <textarea style="resize:none" class="form-control" rows="5" name="content"><?php echo $getAnnouncement['content'] ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
          </div>
          <input type="hidden" name = "id" value = "<?php echo $id ?>">
        </form>
        

        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
