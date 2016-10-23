<?php
	session_start();
	include_once(dirname(__FILE__).'/Controller/LoginController.php');

	$instance = LoginController::getInstance();

	//Already logged-in
	if((isset($_SESSION['user_id'] )&& isset($_SESSION['user_name']))){
		header("Location: index");
		exit;
	}

	if(isset($_POST['submit'])){

		$user_name = $_POST['user_name'];
		$hashedPassword = sha1($_POST['password']);

		$login = $instance->login($user_name, $hashedPassword);

		if($login == 0){
            $_SESSION['error'] = "Your account is not activated";
            header("Location: login.php");
			exit;
        } else if($login == -1){
            $_SESSION['error'] = "Incorrect username and password combination";
			header("Location: login.php");
			exit;
		} else{
			$_SESSION['user_name'] = $login->name;
			$_SESSION['user_id'] = $login->id;
			if($login->isAdmin == 1){
				$_SESSION['is_admin'] = 1;
				header("Location: index");
			}
			else {
				header("Location: index");
			}
		}
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
		<div id="header">

		</div>

		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-4 col-md-offset-4">
					<div class="account-wall" style="margin-bottom:40px">
					<img src="includes/img/nussu-commit-logo-white.png"/>
						<form class="form-signin" method="post" action="login">
							<input name="user_name" type="text" class="form-control" placeholder="username" required autofocus>
							<input name="password" type="password" class="form-control" placeholder="password" required>
							<button name="submit" class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
                        </form>
                    </div>
                    <?php
                        if(isset($_SESSION['error'])){
                            echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                            unset($_SESSION['error']);
                        } ?>
				</div>
            </div>
		</div>
		<footer>
            <div class="container">

                <p class="muted text-center col-sm-6 col-md-4 col-md-offset-4">Copyright &copy; 2014-2015 NUSSU CommIT Technical Cell</p>
            </div>
        </footer>
	</body>
</html>
