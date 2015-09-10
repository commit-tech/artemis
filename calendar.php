<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }	
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    $userController = UserController::getInstance();
    if ($userController->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
        exit;
    }
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
        <?php $page = "report"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <iframe src="https://www.google.com/calendar/embed?height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=nussu.commit%40gmail.com&amp;color=%2328754E&amp;ctz=Asia%2FSingapore" style=" border-width:0 " width="100%" height="500" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>
        </div> 
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>
