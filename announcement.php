<?php
    session_start();

    include_once(dirname(__FILE__).'/Controller/AnnouncementController.php');
    include_once(dirname(__FILE__).'/Controller/UserController.php');

    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }

    $announcement_controller = AnnouncementController::getInstance();
    $announcements = array_reverse($announcement_controller->getAllAnnouncements());
    $userController = UserController::getInstance();
    $is_admin = $userController->isAdmin($_SESSION['user_id']);

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
		<link href="includes/img/logo.ico" rel="icon">
        <link href="includes/css/style.css" rel="stylesheet">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
	</head>
    <body>

        <?php $page = "announcement"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
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
	<div class="container">
	<div class="row">
    <?php if ($is_admin) { ?>
        <div class="col-sm-10">
            <h1>Announcements</h1>
        </div>
        <div class="col-sm-2 well">
            <a href="announcement_add" class="btn btn-default">Add announcement</a>
        </div>
    <?php } else {?>
		<div class="col-sm-12">
            <h1>Announcements</h1>
		</div>
    <?php } ?>
	</div>
	<div class="row">
	<div class="col-sm-12">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Time</th>
                    <?php
                        if ($userController->isAdmin($_SESSION['user_id'])) {
                            echo '<th colspan = 2>Action</th>';
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($announcements as $rows){
                        echo '<tr>';
                        echo '<td>'.$rows['id'].'</td>';
                        echo '<td><b><h3>'.$rows['title'].'</h3></b></td>';
                        echo '<td>'.str_replace("\n", "<br/>", $rows['content']).'</td>';
                        echo '<td>'.$rows['time'].'</td>';
                        if ($userController->isAdmin($_SESSION['user_id'])) {
                            echo '<td> <a class = "btn btn-default" href = announcement_edit.php?id='.$rows['id'].'>Edit </a></td>';
                            echo '<td> <a class = "btn btn-default" href = announcement_delete.php?id='.$rows['id'].'>Delete <a/></td>';
                        }
                        echo '</tr>';
                    }
                ?>
            </tbody>

        </table>
		</div>
		</div>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
