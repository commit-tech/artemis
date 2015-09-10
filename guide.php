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
        <?php $page = "guide"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
		<div class="container">
        <h1>Guide</h1>
        <?php
            $myfile = fopen("data/guide.html", "r") or die("Unable to open file!");
            echo fread($myfile,filesize("data/guide.html"));
        ?>
		</div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
	</body>
</html>
