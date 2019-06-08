<?php
	if (!isset($_SESSION)) {
	  session_start();
	}
	if(isset($_POST['id']) && isset($_POST['pass'])) {
		$userID = "1337";
		$password = "8117";
		$tempUser = $_POST['id'];
		$tempPass = $_POST['pass'];
		if(($tempUser == $userID) && ($tempPass == $password)) {
			if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
			$_SESSION['MM_userID'] = $tempUser;
			$loginRedir = "/Login/Admin/";
			header("Location: ". $loginRedir);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<base href="http://affordablecarmods.com" />

<link href="Resources/css/style.css" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Affordable Car Mods :: Login</title>
<style type="text/css">
form {
	background-color: #ccc;
	width: 200px;
	margin: 10px auto;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
form>#title {
	font-family: 'Open Sans', sans-serif;
	color: #333;
	width: 70px;
	font-size:30px;
	margin: 0 auto;
}
form>#inputAlign {
	background-color: #333;
	width: 150px;
	padding: 10px 25px 10px 25px;
}
form>#inputAlign>input {
	width: 140px;
	-webkit-border-radius: 5px 5px 5px 5px;	-moz-border-radius: 5px 5px 5px 5px; border-radius: 5px 5px 5px 5px;
	border: 0px;
	height: 30px;
	padding: 0 5px 0 5px;
}
form>#submitAlign {
	width: 100px;
	padding: 10px 50px 10px 50px;
	-webkit-border-radius: 0 0 5px 5px;	-moz-border-radius: 0 0 5px 5px; border-radius: 0 0 5px 5px;
}
form>#submitAlign>input {
	width: 100px;
	height: 30px;
	-webkit-border-radius: 5px 5px 5px 5px;	-moz-border-radius: 5px 5px 5px 5px; border-radius: 5px 5px 5px 5px;
	border: 0px;
	background-color: #333;
	color: #FFF;
}
form>#submitAlign>input:hover {
	background-color: #09F;
}
</style>
</head>

<body>
	<div class="wrapper">
    	<div id="header">
            <div id="innerheader">
                <div id="navigation-bar">
                    <a id="item" href="index.php">
                        Home
                    </a>
                    <a id="item" href="Services/index.php">
                        Services
                    </a>
                    <a id="item" href="Projects/index.php" style="width:196px;">
                        Projects
                    </a>
                    <a id="item" href="About/index.php">
                        About Us
                    </a>
                    <a id="item" href="Contact/index.php">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
        <div id="maincentre">
            <div id="main">
            	<form method="post" action="/Login/">
                <div id="title"><span>Login</span></div>
                    <div id="inputAlign" style="-webkit-border-radius: 5px 5px 0 0;	-moz-border-radius: 5px 5px 0 0; border-radius: 5px 5px 0 0;"><input name="id" id="id" type="text" placeholder="Login ID" autocomplete="off"></div>
                    <div id="inputAlign" style="-webkit-border-radius: 0 0 5px 5px;	-moz-border-radius: 0 0 5px 5px; border-radius: 0 0 5px 5px; padding-top:0px;"><input name="pass" id="pass" type="password" autocomplete="off"></div>
                    <div id="submitAlign" style="width:100px;"><input name="submit" type="submit"></div>
                </form>
                <div class="push"></div>
            </div>
        </div>
    </div>
    <div class="footer">
    	<div class="footerinner">
            <p style="float:left; font-size:18px;"><img src="Resources/Images/logo.png" alt="Logo" style="padding-top:1px;" /></p>
            <p style="float:right; text-align:right; padding-top: 8px;">Designed by<br /><a href="" title="Jordittle Degign">Jordittle Design & Media</a><br />&copy; 2011-<?php echo date($y="Y"); ?></p>
        </div>
    </div>
</body>
</html>