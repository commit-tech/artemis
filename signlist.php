<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
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
        
        <?php $page = "sign"; ?>
        <?php include_once(dirname(__FILE__).'/includes/header.php');?>
        <?php include_once(dirname(__FILE__).'/Controller/SignController.php');?>
        <?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
        <div class="container">
        <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            $userID = $_SESSION['user_id'];
            $userController = UserController::getInstance();
            if ($userController->isAdmin($userID) == 0) {
                header("Location: index");
		exit;
            }
        ?>

        <div class="row">
            <div class="col-sm-12">
                <h1>Sign in/out List</h1>
            </div>
        </div>
        <div class="row row-centered text-centered">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
        <?php
            $signController = SignController::getInstance();
            $signList = $signController->getAllSignList();
            ?>
            <table border = "1">
                <tr><td valign="top">
                <h3>Sign in List</h3>
                <table class='table' style='float:left;'>
                    <tr>
                        <th>ID</th>
                        <th>Supervisor Name</th>
                        <th>Time (YYYY-MM-DD hh:mm:ss)</th>
                        <th>Venue</th>
                    </tr>
                    <?php 
                    for ($i = 0; $i < count($signList); ++$i) {
                        if ($signList[$i]['signtype'] != 'in') {
                            continue;
                        }
                        echo "<tr>";
                        echo "<td>".$signList[$i]['id']."</td>";
                        echo "<td>".$userController->getUserName($signList[$i]['supervisor_id'])."</td>";
                        echo "<td>".$signList[$i]['time']."</td>";
                        echo "<td>".$signList[$i]['venue']."</td>";
                    } 
                    ?>
                </table>
                </td><td valign="top">
                <h3>Sign out List</h3>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <th>Supervisor Name</th>
                        <th>Time (YYYY-MM-DD hh:mm:ss)</th>
                        <th>Venue</th>
                    </tr>
                    <?php 
                    for ($i = 0; $i < count($signList); ++$i) {
                        if ($signList[$i]['signtype'] != 'out') {
                            continue;
                        }
                        echo "<tr>";
                        echo "<td>".$signList[$i]['id']."</td>";
                        echo "<td>".$userController->getUserName($signList[$i]['supervisor_id'])."</td>";
                        echo "<td>".$signList[$i]['time']."</td>";
                        echo "<td>".$signList[$i]['venue']."</td>";
                    } 
                    ?>
                </table>
                </td></tr>
            </table>
            </div>
            <div class="col-sm-1"></div>
            </div></div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
