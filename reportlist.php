<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    } 
    include_once(dirname(__FILE__).'/Controller/ProblemController.php');
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    $problemController = ProblemController::getInstance();
    if (isset($_GET['filter']) && $_GET['filter'] == 'all') {
        $problemList = $problemController->getAllReportList();
    } else if (isset($_GET['filter']) && $_GET['filter'] == 'critical') {
        $problemList = $problemController->getUnfixedCriticalReportList();
    } else {
        $problemList = $problemController->getUnfixedReportList();
    }
    $problemList = array_reverse($problemList);
    $userController = UserController::getInstance();
    $HTML_NO = "&#10008";
    $HTML_YES = "&#10004"; 
    $canEdit = $userController->getCell($_SESSION['user_id']) == "Technical" ? 1 : 0;
    if (isset($_GET['id'])) {
        if ($canEdit) {
            if (isset($_GET['change']) && $_GET['change'] == 'blocked') {
                $problemController->changeBlockStatus($_SESSION['user_id'], $_GET['id']);
            }
            if (isset($_GET['change']) && $_GET['change'] == 'fixed') {
                $problemController->changeFixStatus($_SESSION['user_id'], $_GET['id']);
            }
            if (isset($_GET['change']) && $_GET['change'] == 'fixable') {
                $problemController->changeFixableStatus($_SESSION['user_id'], $_GET['id']);
            }
            if (isset($_GET['change']) && $_GET['change'] == 'critical') {
                $problemController->changeCriticalStatus($_SESSION['user_id'], $_GET['id']);
            }
        }
        header("Location: reportlist");
        exit;
    }
    if (isset($_POST['id'])) {
        if ($canEdit) {
            $problemController->changeRemarks($_SESSION['user_id'], $_POST['remarks'], $_POST['id']);
        }
        header("Location: reportlist");
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
        <?php $page = "report"; ?>
        <?php include_once(dirname(__FILE__).'/includes/header.php');?>
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
        ?>

        <div class="row">
            <div class="col-sm-12">
                <h1> Computer Problem Report List <?php
                    if (isset($_GET['filter']) && $_GET['filter'] == 'all') {
                        echo "(All)";
                    } else if (isset($_GET['filter']) && $_GET['filter'] == 'critical') {
                        echo "(Critical)";
                    } else {
                        echo "(Unfixed and Fixable)";
                    }
                ?> </h1>
            </div>
        </div>
        <div class="row row-centered text-centered">
        <div class="col-sm-1"></div>
        <a class='btn btn-default' href='reportlist'>Show Unfixed and Fixable Problem Only</a>
        <a class='btn btn-default' href='reportlist?filter=critical'>Show Critical Unfixed Problem Only</a>
        <a class='btn btn-default' href='reportlist?filter=all'>Show All Problems</a>
        <a class='btn btn-default' href='report'>Submit a new report</a>
        <div class="col-sm-10">
            <table class='table' style='float:left;'>
                <tr>
                    <th>ID</th>
                    <th>Reporter Name</th>
                    <th>Venue</th>
                    <th>PC Number</th>
                    <th width="200%">Description</th>
                    <th>Critical?</th>
                    <th>Blocked? (CL Only)</th>
                    <th>Fixed?</th>
                    <th>Fixable?</th>
                    <th width="300%">Remarks</th>
                    <th>Last updated by</th>
                    <th>Last updated at (PST)</th>
                </tr>
                <?php
                for ($i = 0; $i < count($problemList); ++$i) {
                    echo "<tr>";
                    echo "<td>".$problemList[$i]['id']."</td>";
                    echo "<td>".$userController->getUserName($problemList[$i]['reporter_id'])."</td>";
                    echo "<td>".$problemList[$i]['venue']."</td>";
                    echo "<td>".$problemList[$i]['pc_number']."</td>";
                    echo "<td>".$problemList[$i]['description']."</td>";
                    $isCritical = $problemList[$i]['critical'] ? $HTML_YES : $HTML_NO;
                    $isBlocked = $problemList[$i]['blocked'] ? $HTML_YES : $HTML_NO;
                    $isFixed = $problemList[$i]['fixed'] ? $HTML_YES : $HTML_NO;
                    $isFixable = $problemList[$i]['fixable'] ? $HTML_YES : $HTML_NO;

                    if ($canEdit) {
                        $params = array_merge($_GET, array("id" => $problemList[$i]['id'], "change" => "critical"));
                        $new_query_string = http_build_query($params);
                        echo "<td><a href='reportlist?$new_query_string'>$isCritical</a></td>";
                    } else {
                        echo "<td>".$isCritical."</td>";
                    }

                    if ($canEdit) {
                        $params = array_merge($_GET, array("id" => $problemList[$i]['id'], "change" => "blocked"));
                        $new_query_string = http_build_query($params);
                        echo "<td><a href='reportlist?$new_query_string'>$isBlocked</a></td>";
                    } else {
                        echo "<td>".$isBlocked."</td>";
                    }

                    if ($canEdit) {
                        $params = array_merge($_GET, array("id" => $problemList[$i]['id'], "change" => "fixed"));
                        $new_query_string = http_build_query($params);
                        echo "<td><a href='reportlist?$new_query_string'>$isFixed</a></td>";
                    } else {
                        echo "<td>".$isFixed."</td>";
                    }

                    if ($canEdit) {
                        $params = array_merge($_GET, array("id" => $problemList[$i]['id'], "change" => "fixable"));
                        $new_query_string = http_build_query($params);
                        echo "<td><a href='reportlist?$new_query_string'>$isFixable</a></td>";
                    } else {
                        echo "<td>".$isFixable."</td>";
                    }

                    if ($canEdit) {
                        echo "<td>";
                        echo $problemList[$i]['remarks'];
                        echo "<form method='POST' id='remarks_form'>";
                        echo "<input type='hidden' name='change' value='remarks'>";
                        echo "<input type='hidden' name='id' value='".$problemList[$i]['id']."'>";
                        echo "<textarea name='remarks'>";
                        echo "</textarea>";
                        echo "<input type='submit' value='Change Remark'>";
                        echo "</form>";
                        echo "</td>";
                    } else {
                        echo "<td>".$problemList[$i]['remarks']."</td>";
                    }

                    if (is_null($problemList[$i]['handler_id'])) {
                        echo "<td></td>";
                    } else {
                        echo "<td>".$userController->getUserName($problemList[$i]['handler_id'])."</td>";
                    }
                    echo "<td>".$problemList[$i]['last_updated']."</td>";
                    echo "<tr/>";
                } 
                ?>
            </table>
            </div>
            <div class="col-sm-1"></div>
            </div></div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
  </body>
</html>
