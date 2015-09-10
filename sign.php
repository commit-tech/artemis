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
<?php include(dirname(__FILE__).'/Controller/SignController.php');?>
<?php
    $userID = $_SESSION['user_id'];
    if (isset($_POST['submit'])) {
        $signController = SignController::getInstance();
        $venue = $_POST['venue'];
        $signType = $_POST['sign'];
        $sign = new Sign(null, $userID, null, $venue, $signType);
        $user = new User($userID, null, null, null, null, null, null, null);
        $signController->addSign($user,$sign);
        $_SESSION['success'] = 'Succesfully signed in/out';
        header('Location: index');
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
			<?php
			$userController = UserController::getInstance();
			if ($userController->isAdmin($userID) == 1) { ?>
			<div class="col-sm-10">
			<h1>Sign In/Out</h1>
			</div>
			<div class="col-sm-2 well">
				<a class='btn btn-default' href='signlist'>Sign in/out list</a>
			</div>
				<?php
			}else{?>
			<div class="col-sm-12">
			<h1>Sign In/Out</h1>
			</div>
			<?php
			}
			?>
			</div>

		

        <form class="form-horizontal well" action="sign" method="post">
			<div class="form-group">
				<label class="control-label col-xs-2">Name</label>
				<div class="col-xs-10">
				<?php echo $_SESSION['user_name']; ?>
				</div>
			</div>

			<div class="form-group">
				<label for="venue" class="control-label col-sm-2">
				Venue
				</label>
				<div class="btn-group col-sm-4" data-toggle="buttons">
					<label class="btn btn-default"><input type="radio" name="venue" value="yih" required />YIH</label>
					<label class="btn btn-default"><input type="radio" name="venue" value="cl" />CL</label>
				</div>
            </div>
			
			<div class="form-group">
				<label class="control-label col-sm-2">
				Action
				</label>
				<div class="btn-group col-sm-4" data-toggle="buttons">
					<label class="btn btn-default"><input type="radio" name="sign" value="in" required />Sign in</label>
					<label class="btn btn-default"><input type="radio" name="sign" value="out" />Sign out</label>
				</div>
			</div>
			
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
        </form>
		</div>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
