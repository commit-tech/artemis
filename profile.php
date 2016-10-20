<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }

    $user_controller = UserController::getInstance();
    $id = $_SESSION['user_id'];
    if(isset($_GET['id']) && $user_controller->isAdmin($_SESSION['user_id'])){
        $id = $_GET['id'];
    } else if(isset($_GET['id'])){
        header("Location: profile");
        exit;
    }
    $userprofile = $user_controller->getUserInfo($id);
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

        <?php $page = "user"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
		<div class="container">
        <?php 

            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert">X</button>'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }

            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            } 

            if ($user_controller->isAdmin($_SESSION['user_id']) != 0 || isset($_SESSION['original_login'])) {
                echo "<a class=\"btn btn-primary\" href=\"loginas\">Login as</a>";
            }

        ?>
        <h1> User Profile</h1>
        <br/>
        <dl class="dl-horizontal">
            <dt>Username: </dt> <?php echo '<dd>'.$userprofile['name'].'</dd>';?>
            <dt>Matric number: </dt> <?php echo '<dd>'.$userprofile['matric_number'].'</dd>';?>
            <dt>Cell: </dt> <?php echo '<dd>'.$userprofile['cell'].'</dd>';?>
            <dt>Position: </dt> <?php echo '<dd>'.$userprofile['position'].'</dd>';?>
            <dt>Contact: </dt> <?php echo '<dd>'.$userprofile['contact'].'</dd>';?>
            <dt>Email: </dt> <?php echo '<dd>'.$userprofile['email'].'</dd>';?>
            <dt>New notifications: </dt> <dd><?php echo $userprofile['notification']==1?"Subscribed":"Not subscribed";?></dd>
            <br/>
            <dt><a href="user?id=<?php echo $id;?>" class="btn btn-default"> Update profile</a></dt>
        </dl>

        <br/>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
