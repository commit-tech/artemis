<?php
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }
    $user_controller = UserController::getInstance();
    function isNotHidden($user) {
        $hiddenUsers = array("admin", "comcen");
        return !in_array($user['name'], $hiddenUsers, true);
    }
    function isTargetedUser($user) {
        $target = $_GET["name"];
        return $user['name'] == $target;
    }
    if (!empty($_GET["name"])) {
        $members = array_filter($user_controller->getAllUser(), 'isTargetedUser');
    } else {
        $members = array_filter($user_controller->getAllUser(), 'isNotHidden');
    }
    $is_admin = $user_controller->isAdmin($_SESSION['user_id']);
    if ($user_controller->getDutyStatus($_SESSION['user_id']) == 0) {
        header("Location: tracking");
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
        <script src="includes/js/jquery.tablesorter.min.js"></script>
        <script>
            function checkAll(){
                var checkboxes = document.getElementsByTagName('input'), val = null;
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox'){
                        if (val === null) val = checkboxes[i].checked;
                        checkboxes[i].checked = val;
                    }
                }
            }
        </script>
    </head>
    <body>
        <?php $page = "members"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <?php
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            ?>
            <div class="row">
                <?php if ($is_admin) { ?>
                    <form action="members_manage" method="post">
                    <div class="col-sm-7">
                        <h1>Members List</h1>
                    </div>
                    <div class="col-sm-5 well">
                        <button type="submit" name="delete_user" class="btn btn-default" value="delete_user">Delete Selected</button>
                        <button type="submit" name="reset_password" class="btn btn-default" value="reset_password">Reset Password</button>
                        <button type="submit" name="change_status" class="btn btn-default" value="change_status">Change Status</button>
                        <a class="btn btn-default" href="user_add">Add User</a>
                    </div>
                <?php }else{ ?>
                    <div class="col-sm-12">
                        <h1>Members List</h1>
                    </div>
                <?php } ?>
                    <table class="table table-hover" id="members">
                        <thead>
                            <tr>
                                <?php if($is_admin){?><th class="text-center sorter-false"><input id="0" type="checkbox" onClick="checkAll()"></th><?php } ?>
                                <th class="text-center">Name</th>
                                <th class="text-center">Matric</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Cell</th>
                                <th class="text-center">Position</th>
                                <?php if($is_admin) { ?><th class="text-center">Status</th><?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cnt = 0;
                                foreach($members as $row){
                                    $cnt++;
                                    echo '<tr>';
                                    if($is_admin){
                                        echo "<td><input id=".$cnt." name='users[]' type='checkbox' value=".$row['id']."></td>";
                                    }
                                    if($is_admin) echo "<td><a href='user.php?id={$row['id']}'>".$row['name']."</a></td>";
                                    else echo '<td>'.$row['name'].'</td>';
                                    echo '<td>'.$row['matric_number'].'</td>';
                                    echo '<td>'.$row['email'].'</td>';
                                    echo '<td>'.$row['contact'].'</td>';
                                    echo '<td>'.$row['cell'].'</td>';
                                    echo '<td>'.$row['position'].'</td>';
                                    if($is_admin) {
                                        if($row['status'] == 1) echo "<td>Activated</td>";
                                        else echo "<td>Deactivated</td>";
                                    }
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php if ($is_admin) { ?>
                    </form>
                    <?php } ?>
            </div>
        </div>
        <script src="includes/js/members.js" type="text/javascript"></script>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
    </body>
</html>
