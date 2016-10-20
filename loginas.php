<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }
?>
<?php include_once(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include_once(dirname(__FILE__).'/Controller/UserController.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
    $userID = $_SESSION['user_id'];
    if (!isset($_SESSION['original_login']) && $userController->isAdmin($userID) == 0) {
        header("Location: index");
        exit;
    }
    if (isset($_SESSION['original_login'])) {
        $_SESSION['user_id'] = $_SESSION['original_login'];
        $_SESSION['user_name'] = $userController->getUserName($_SESSION['user_id']);
        unset($_SESSION['original_login']);
    }
    if(isset($_SESSION['error'])){
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if (isset($_POST['loginas']) && $_POST['loginas'] != 'unset') {
        $_SESSION['original_login'] = $_SESSION['user_id'];
        $_SESSION['user_id'] = $_POST['loginas'];
        $_SESSION['user_name'] = $userController->getUserName($_SESSION['user_id']);
        header("Location: index");
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
        <?php $page = "loginas"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <div align="center" class="row">
                    <h1>Login as</h1>
            </div>
        <div class="row">
        <form action="loginas" method="post">
            <p align="center">
            <select id="loginas" name="loginas">
                <?php
                    $allUsers = $userController->getAllUser();
                    usort($allUsers, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                    echo "<option value='unset'>Choose wisely</option>";
                    for ($i = 0; $i < count($allUsers); ++$i) {
                        echo "<option value='".$allUsers[$i]['id']."'>".$allUsers[$i]['name']."</option>";
                    }
                ?>
            </select>
            <input type='submit' class='btn btn-primary'/>
            </p>
        </form>
        </div>
        </div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
  </body>

</html>
