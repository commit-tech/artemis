<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    $user_controller = UserController::getInstance();

    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }

    $id = $_SESSION['user_id'];
    $is_admin = $user_controller->isAdmin($_SESSION['user_id']);
    if(isset($_GET['id']) && $is_admin){
        $id = $_GET['id'];
    } else if(isset($_GET['id'])){
        header("Location: user");
        exit;
    }

	if(isset($_POST['submit'])){
        $subscribe = isset($_POST['subscribe']);

        if(!empty($_POST['password'])){
            if($_POST['password'] != $_POST['password_confirm']){
                $_SESSION['error'] = "New password does not match.";
                header("Location: user");
                exit;
            }else{
                $hashedPassword = sha1($_POST['password']);
                $edit = $user_controller->editUser($id, 
                    array("pass" => $hashedPassword,
                    "email" => $_POST['email'],
                    "contact" => $_POST['contact'],
                    "subscribe" => $subscribe,
                    "cell" => $_POST['cell'],
                    "position" => $_POST['position'])
                );
            }
        }else{
            $edit = $user_controller->editUser($id, 
                array("pass" => "no",
                "email" => $_POST['email'],
                "contact" => $_POST['contact'],
                "cell" => $_POST['cell'],
                "position" => $_POST['position'],
                "subscribe" => $subscribe));
        }
        
        if($edit){
            $_SESSION['edit_status'] = 1;
            $_SESSION['success'] = "Update success!";
            header("Location: profile?id=".$id);
            exit;
        } 
        else{
            $_SESSION['edit_status'] = 0;
            $_SESSION['error'] = "Something went wrong.";
            header("Location: user?id=".$id);
            exit;
        } 
        
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
        <?php 
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            } 
            
        ?>
        <div class="container">
        <h1> User Update profile</h1>
        <br/>
        <form class="form-horizontal" action="user<?php if($id!=$_SESSION['user_id']) echo "?id={$id}";?>" method="post">
            <div class="form-group">
                <label class = "col-sm-2 control-label">Username: </label>
                <div class="col-sm-6">
                    <input type = "text" name="username" class="form-control" required value="<?php echo $userprofile['name'];?>" disabled>
                </div>
            </div>
            <?php if ($is_admin) { ?>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Cell: </label>
                <div class="col-sm-6">
                    <select name="cell" class="form-control">
                        <option value="Presidential" <?php if($userprofile['cell']=="Presidential") echo "selected=\"selected\""; ?>>Presidential</option>
                        <option value="Center and Ops" <?php if($userprofile['cell']=="Center and Ops") echo "selected=\"selected\""; ?>>Center and Ops</option>
                        <option value="Technical" <?php if($userprofile['cell']=="Technical") echo "selected=\"selected\""; ?>>Technical</option>
                        <option value="Publicity" <?php if($userprofile['cell']=="Publicity") echo "selected=\"selected\""; ?>>Publicity</option>
                        <option value="Marketing" <?php if($userprofile['cell']=="Marketing") echo "selected=\"selected\""; ?>>Marketing</option>
                        <option value="Welfare" <?php if($userprofile['cell']=="Welfare") echo "selected=\"selected\""; ?>>Welfare</option>
                        <option value="Training" <?php if($userprofile['cell']=="Training") echo "selected=\"selected\""; ?>>Training</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Position: </label>
                <div class="col-sm-6">
                    <select name="position" class="form-control">
                        <option value="Manager" <?php if($userprofile['position']=="Manager") echo "selected=\"selected\""; ?>>Manager</option>
                        <option value="Asst. Manager" <?php if($userprofile['position']=="Asst. Manager") echo "selected=\"selected\""; ?>>Asst. Manager</option>
                        <option value="Subcom" <?php if($userprofile['position']=="Subcom") echo "selected=\"selected\""; ?>>Subcom</option>
                        <option value="Chairman" <?php if($userprofile['position']=="Chairman") echo "selected=\"selected\""; ?>>Chairman</option>
                        <option value="Vice Chairman" <?php if($userprofile['position']=="Vice Chairman") echo "selected=\"selected\""; ?>>Vice Chairman</option>
                        <option value="Secretary" <?php if($userprofile['position']=="Secretary") echo "selected=\"selected\""; ?>>Secretary</option>
                        <option value="Treasurer" <?php if($userprofile['position']=="Treasurer") echo "selected=\"selected\""; ?>>Treasurer</option>
                    </select>
                </div>
            </div>
            <?php } else {?>
            <input type="hidden" name="cell" value="<?php echo $userprofile['cell'];?>">
            <input type="hidden" name="position" value="<?php echo $userprofile['position'];?>">
            <?php }?>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Matric number: </label>
                <div class="col-sm-6">
                    <input type = "text" name="matric" class="form-control" required value="<?php echo $userprofile['matric_number'];?>" disabled>
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">New password: </label>
                <div class="col-sm-6">
                    <input type = "password" name="password" pattern=".{8,}" title="Minimum of 8 characters" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Confirm Password: </label>
                <div class="col-sm-6">
                    <input type = "password" name="password_confirm" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label">Contact: </label>
                <div class="col-sm-6">
                    <input type = "text" name="contact" class="form-control" required value="<?php echo $userprofile['contact'];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Email: </label>
                <div class="col-sm-6">
                    <input type="email" name="email" class="form-control" required value="<?php echo $userprofile['email'];?>">
                </div>
            </div>
             <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="subscribe" value = "1" <?php echo $userprofile['notification']==1? "checked" : "" ;?> > Subscribe to new notifications
                    </label>
                  </div>
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
