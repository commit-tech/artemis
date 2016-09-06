<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Utils/Mailer.php');
    include_once(dirname(__FILE__).'/Utils/Constants.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }

    $user_controller = UserController::getInstance();
    if(!$user_controller->isAdmin($_SESSION['user_id'])){
        header("Location: index");
        exit;
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

	if(isset($_POST['submit'])){
        $user = new Member(1, $_POST['username'], $_POST['matric'], $_POST['contact'], $_POST['email'], $_POST['cell'], $_POST['position'], 1);
        $pass = '123456';
        $user_controller->addUser($user, $pass);

        /*$target[] = $user_controller->getUserByMatric($_POST['matric']);
        $mailSubject = "NUSSU commIT New Account";
        $mailBody = "Dear {$_POST['username']},\n\nWelcome to NUSSU commIT!\n\nAs part of the duty management, we have created an account for you in our system. The details are:\n
                Username: {$_POST['username']}\n
                Password: {$pass}\n\nLogin to ".DOMAIN." with the above details, change your password immediately, and check your particulars.".FOOTER_MESSAGE;
        sendMail($mailSubject, $mailBody, $target);*/
        $_SESSION['success'] = "User Added!";
        header("Location: user_add");
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

        <?php $page = "user"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <?php 
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            } 
            
        ?>
        <div class="container">
        <?php
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            ?>
        <h1>Add new user</h1>
        <br/>
        <form class="form-horizontal" action="user_add" method="post">
            <div class="form-group">
                <label class = "col-sm-2 control-label">Cell: </label>
                <div class="col-sm-6">
                    <select name="cell" class="form-control">
                        <option value="Presidential">Presidential</option>
                        <option value="Center and Ops">Center and Ops</option>
                        <option value="Technical">Technical</option>
                        <option value="Publicity">Publicity</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Welfare">Welfare</option>
                        <option value="Training">Training</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Position: </label>
                <div class="col-sm-6">
                    <select name="position" class="form-control">
                        <option value="Manager">Manager</option>
                        <option value="Asst. Manager">Asst. Manager</option>
                        <option value="Subcom">Subcom</option>
                        <option value="Chairman">Chairman</option>
                        <option value="Vice Chairman">Vice Chairman</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Username: </label>
                <div class="col-sm-6">
                    <input type = "text" name="username" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Matric number: </label>
                <div class="col-sm-6">
                    <input type = "text" name="matric" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Contact: </label>
                <div class="col-sm-6">
                    <input type = "text" name="contact" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Email: </label>
                <div class="col-sm-6">
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
        <br/>
        </div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
