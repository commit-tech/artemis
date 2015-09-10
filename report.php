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
                <div class="col-md-4">
                    <iframe src="https://docs.google.com/forms/d/1cZ0fyUuhvfNF3r0VUnWPO8Y-aKDFRCYeNi02lGBNhkQ/viewform?embedded=true" width="360" height="500" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
                </div>
                <div class="col-md-8">
                    <iframe src="https://docs.google.com/spreadsheets/d/1cMZ2uLNmva6oB9Lx6bKt_C5YFiSRfIRerKx4munNhCM/pubhtml?widget=true&amp;headers=false" width="760" height="500" frameborder="0" marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
        </div> 
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>
