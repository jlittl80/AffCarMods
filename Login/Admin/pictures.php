<?php require_once('../../Connections/localhost.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
//Check User is logged in
if (!(isset($_SESSION['MM_userID']))) {
	$MM_restrictGoTo = "../index.php";
	header("Location: ". $MM_restrictGoTo); 
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if (((isset($_POST['projectSelect'])) && ($_POST['projectSelect'] != "")) && ((isset($_POST['pictureSelect']) && ($_POST['pictureSelect'] != ""))) && ((isset($_POST['MM_removePicture']) && ($_POST['MM_removePicture'] == "removePicture")))) {
	$folder = "../../Resources/Images/Projects/".$_POST['projectSelect'];
	if(unlink($folder."/".$_POST['pictureSelect']) == true) {
		$deleteSQL = sprintf("DELETE FROM projectpictures WHERE projectID=%s AND pictureID = %s", GetSQLValueString($_POST['projectSelect'], "int"), GetSQLValueString($_POST['pictureSelect'], "text"));
		mysql_select_db($database_localhost, $localhost);
		$Result1 = mysql_query($deleteSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));
		
		$deleteGoTo = "pictures.php?s=t";
		if (isset($_SERVER['QUERY_STRING'])) {
			$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
			$deleteGoTo .= $_SERVER['QUERY_STRING'];
		}
		header(sprintf("Location: %s", $deleteGoTo));
	} else {
		die(header(sprintf("Location: %s", "error.php?error="."Picture didnt delete")));	
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addPicture")) {
	
	$insertSQL = sprintf("INSERT INTO projectpictures (projectID, pictureID) VALUES (%s, %s)", GetSQLValueString($_POST['projectSelect'], "int"), GetSQLValueString($_FILES["imageUpload"]["name"], "text"));
	
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["imageUpload"]["name"]);
	$extension = end($temp);
	$folder = "../../Resources/Images/Projects/".$_POST['projectSelect'];
	
	if ((($_FILES["imageUpload"]["type"] == "image/gif") || ($_FILES["imageUpload"]["type"] == "image/jpeg") || ($_FILES["imageUpload"]["type"] == "image/jpg") || ($_FILES["imageUpload"]["type"] == "image/pjpeg") || ($_FILES["imageUpload"]["type"] == "image/x-png") || ($_FILES["imageUpload"]["type"] == "image/png")) && in_array(strtolower($extension), $allowedExts)) {
		if ($_FILES["imageUpload"]["error"] == 0) {
			if (!file_exists($folder)) {
				mkdir($folder);
			}
			if (!file_exists($folder."/".$_FILES["imageUpload"]["name"])) {
				move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $folder."/".$_FILES["imageUpload"]["name"]);
			} else {
				die(header(sprintf("Location: %s", "error.php?error="."File Exists")));	
			}
		} else {
			die(header(sprintf("Location: %s", "error.php?error=".$_FILES["imageUpload"]["error"])));
		}
	} else {
		die(header(sprintf("Location: %s", "error.php?error=The file isnt the correct file type")));
	}
	
	mysql_select_db($database_localhost, $localhost);
	$Result1 = mysql_query($insertSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));
	
	$insertGoTo = "pictures.php?s=t";
	if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_rsSelectProjects = "SELECT * FROM projects ORDER BY projectID ASC";
$rsSelectProjects = mysql_query($query_rsSelectProjects, $localhost) or die(mysql_error());
$row_rsSelectProjects = mysql_fetch_assoc($rsSelectProjects);
$totalRows_rsSelectProjects = mysql_num_rows($rsSelectProjects);

mysql_select_db($database_localhost, $localhost);
$query_rsProjectPictureList = "SELECT * FROM projectpictures JOIN projects WHERE projectpictures.projectID = projects.projectID";
$rsProjectPictureList = mysql_query($query_rsProjectPictureList, $localhost) or die(mysql_error());
$row_rsProjectPictureList = mysql_fetch_assoc($rsProjectPictureList);
$totalRows_rsProjectPictureList = mysql_num_rows($rsProjectPictureList);

mysql_select_db($database_localhost, $localhost);
$query_rsRemoveProjectSelect = "SELECT * FROM projects WHERE projectID IN ( SELECT projectID FROM projectpictures GROUP BY projectID )";
$rsRemoveProjectSelect = mysql_query($query_rsRemoveProjectSelect, $localhost) or die(mysql_error());
$row_rsRemoveProjectSelect = mysql_fetch_assoc($rsRemoveProjectSelect);
$totalRows_rsRemoveProjectSelect = mysql_num_rows($rsRemoveProjectSelect);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="../../Resources/css/style.css" rel="stylesheet" type="text/css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
<style type="text/css">
	form[name~=addPicture] {
		float: left;
		background-color: #ccc;
		width: 200px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		padding: 10px 0 10px 0;
		margin-top: 10px;
		text-align: center;
		font-family: 'Open Sans', sans-serif; 
		color: #333;
		font-size:20px;
	}
	form[name~=addPicture] #projectDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=addPicture] #projectDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		margin-bottom: 0px;
	}
	form[name~=addPicture] #imageUploadDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
		color: #FFF;
	}
	form[name~=addPicture] #imageUploadDiv #inputContainer {
		position: relative;
		overflow: hidden;
		width: 170px;
		height: 25px;
		font-size: 13px;
		margin: 0 auto;
		background-color: #ccc;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		vertical-align:middle;
		margin-bottom: 10px;
		padding-top: 5px;
	}
	form[name~=addPicture] #imageUploadDiv #inputContainer:hover {
		background-color: #09F;
	}
	form[name~=addPicture] #imageUploadDiv #inputContainer input[type~=file] {	
		position: absolute;
		top: 0;
		right: 0;
		margin: 0;
		padding: 0;
		font-size: 20px;
		cursor: pointer;
		opacity: 0;
		filter: alpha(opacity=0);
	}
	form[name~=addPicture] input[type~=submit] {
		width: 100px;
		height: 30px;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		border: 0px;
		background-color: #333;
		color: #FFF;
		margin: 10px 10px 0 10px;
	}
	form[name~=addPicture] input[type~=submit]:hover {
		background-color: #09F;
	}
	
	#pictureList {
		margin: 10px;
		float: left;
		width: 580px;
		background-color:#ccc;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		padding: 10px 0 0 0;
		text-align: center;
		font-family: 'Open Sans', sans-serif; 
		color: #333;
		font-size:20px;
	}
	#pictureList a {
		text-decoration:none;
		color: #CCC;
	}
	#pictureList a:link {
		color: #CCC;
	}
	#pictureList a:visited {
		color: #CCC;
	}
	#pictureList a:hover {
		color: #CCC;
	}
	#pictureList a:active {
		color: #CCC;
	}
	#pictureList table {
		margin-top: 5px;
		width: 100%;
		text-align: left;
		font-size:13px;
		background-color: #333;
		color: #FFF;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	#pictureList table #titleBar {
		font-size: 16px;
	}
	#pictureList table tr:first-child td:first-child {
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		border-top-left-radius: 5px;
	}
	#pictureList table tr:first-child td:last-child {
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		border-top-right-radius: 5px;
	}
	#pictureList table tr:last-child td:first-child {
		-moz-border-radius-bottomleft: 5px;
		-webkit-border-bottom-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	#pictureList table tr:last-child td:last-child {
		-moz-border-radius-bottomright: 5px;
		-webkit-border-bottom-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}
	
	form[name~=removePicture] {
		float: right;
		background-color: #ccc;
		width: 200px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		padding: 10px 0 0 0;
		margin-top: 10px;
		text-align: center;
		font-family: 'Open Sans', sans-serif; 
		color: #333;
		font-size:20px;
	}
	form[name~=removePicture] #projectDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=removePicture] #projectDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		margin-bottom: 0px;
	}
	form[name~=removePicture] #projectDiv a {
		padding: 5px 0 5px 0;
		background-color: #ccc;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
		color: #FFF;
		width: 170px;
		display: inline-block;
		text-decoration: none;
		text-align: center;
		cursor: pointer;
		font-size:14px;
		margin-bottom: 5px;
	}
	form[name~=removePicture] #projectDiv a:hover {
		background-color: #09F;
	}
	form[name~=removePicture] #serviceDiv {
		width: 200px;
		padding: 0 0 10px 0;
		background-color: #333;		
		color: #FFF;
	}
	form[name~=removePicture] #serviceDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		margin-bottom: 0px;
	}
	form[name~=removePicture] #bottom {
		height:5px;
		background-color: #333;
		width: 200px;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
	}
	form[name~=removePicture] input[type~=submit] {
		width: 100px;
		height: 30px;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		border: 0px;
		background-color: #333;
		color: #FFF;
		margin: 10px 10px 10px 10px;
	}
	form[name~=removePicture] input[type~=submit]:hover {
		background-color: #09F;
	}
</style>

<script type="text/javascript">
function removeGetPictures() {
	var projectID = $("#removeProjectSelect").val()
	url = "getData.php";
	var posting = $.post(url, {projectID:projectID, function:"removeGetPictures"});
	
	posting.done(function(data) {
		$("#removePicture ").children("#serviceDiv").remove()
		$("#removePicture ").children("#removeTag").remove()
		$("#removePicture ").children("#projectDiv").after('<div id="serviceDiv">Service')
		$("#removePicture ").children("#serviceDiv").append(data)
		$("#removePicture ").children("#serviceDiv").append('</div>')
		$("#removePicture ").children("#bottom").after('<input name="removeTag" id="removeTag" type="submit" value="Remove" />')
	});
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Affordable Car Mods :: Admin</title>
</head>

<body>
	<div id="header">
        <div id="innerheader">
            <div id="navigation-bar">
                <a id="item" href="index.php">
                    Services
          </a>
                <a id="item" href="projects.php">
                    Projects
          </a>
                <a id="item-selected" href="pictures.php" style="width:196px;">
                    Pictures
          </a>
                <a id="item" href="tags.php">
                   Tags
          </a>
                <a id="item" href="<?php echo $logoutAction ?>">
                    Logout
              </a>
            </div>
        </div>
    </div>
	<div class="wrapper">
        <div id="maincentre">
            <div id="main">
            	<form action="<?php echo $editFormAction; ?>" name="addPicture" method="POST" enctype="multipart/form-data">
                	Add Picture
                    <div id="projectDiv">
                    	Project
                    	<select name="projectSelect">
                    	  	<?php
							do {  
							?>
                    	  	<option value="<?php echo $row_rsSelectProjects['projectID']?>"><?php echo "(".$row_rsSelectProjects['projectID'].") ".$row_rsSelectProjects['carMake']." ".$row_rsSelectProjects['carModel']." ".$row_rsSelectProjects['carYear']?></option>
                    	  	<?php
							} while ($row_rsSelectProjects = mysql_fetch_assoc($rsSelectProjects));
							$rows = mysql_num_rows($rsSelectProjects);
							if($rows > 0) {
								mysql_data_seek($rsSelectProjects, 0);
								$row_rsSelectProjects = mysql_fetch_assoc($rsSelectProjects);
							}
							?>
                    	</select>
          			</div>
                    <div id="imageUploadDiv">
                    	Picture
                        <div id="inputContainer">
                        	Upload
                    		<input name="imageUpload" id="imageUpload" type="file" autocomplete="off" width="170" />
                        </div>
                    </div>
                    <input name="insertService" type="submit" value="Add" />
                    <input type="hidden" name="MM_insert" value="addPicture" />
                </form>
                <div id="pictureList">
                	Projects
                    <table>
                    	<tr id="titleBar">
                        	<td width="10%">ID</td>
                            <td width="20%">Make</td>
                            <td width="20%">Model</td>
                            <td width="20%">Year</td>
                            <td width="30%">Picture</td>
                        </tr>
                    	<?php
                            do {  
                        ?>
                        <tr>
                        	<td><?php echo $row_rsProjectPictureList['projectID']?></td>
                            <td><?php echo $row_rsProjectPictureList['carMake']?></td>
                            <td><?php echo $row_rsProjectPictureList['carModel']?></td>
                            <td><?php echo $row_rsProjectPictureList['carYear']?></td>
                            <td><a href="../../Resources/Images/Projects/<?php echo $row_rsProjectPictureList['projectID'] ?>/<?php echo $row_rsProjectPictureList['pictureID'] ?>" target="_new"><?php echo $row_rsProjectPictureList['pictureID'] ?></a></td>
                        </tr>
                        <?php
                            } while ($row_rsProjectPictureList = mysql_fetch_assoc($rsProjectPictureList));
                            $rows = mysql_num_rows($rsProjectPictureList);
                            if($rows > 0) {
                                mysql_data_seek($rsProjectPictureList, 0);
                                $row_rsProjectPictureList = mysql_fetch_assoc($rsProjectPictureList);
                            }
                        ?>
                    </table>
                </div>
                <form name="removePicture" id="removePicture" method="POST">
                	Remove Picture
                    <div id="projectDiv">
                    	Project
                    	<select name="projectSelect" id="removeProjectSelect" onchange="removeGetPictures()">
                    		<?php
							do {  
							?>
                            <option value="<?php echo $row_rsRemoveProjectSelect['projectID']?>"><?php echo "(".$row_rsRemoveProjectSelect['projectID'].") ".$row_rsRemoveProjectSelect['carMake']." ".$row_rsRemoveProjectSelect['carModel']." ".$row_rsRemoveProjectSelect['carYear']?></option>
                    	    <?php
							} while ($row_rsRemoveProjectSelect = mysql_fetch_assoc($rsRemoveProjectSelect));
  							$rows = mysql_num_rows($rsRemoveProjectSelect);
  							if($rows > 0) {
      							mysql_data_seek($rsRemoveProjectSelect, 0);
	  							$row_rsRemoveProjectSelect = mysql_fetch_assoc($rsRemoveProjectSelect);
  							}
							?>
                    	</select>
                        <a onclick="removeGetPictures()">Search</a>
                    </div>
                    
                    <div id="bottom"></div>
                  	<input type="hidden" name="MM_removePicture" value="removePicture" />
                </form>
                <div style="float:left;">NOTE: Upload a photo called 'default.jpg' for each project</div>
              	<div class="push"></div>
            </div>
        </div>
    </div>
    <div class="footer">
    	<div class="footerinner">
            <p style="float:left; font-size:18px;"><img src="../../Resources/Images/logo.png" alt="Logo" style="padding-top:1px;" /></p>
            <p style="float:right; text-align:right; padding-top: 8px;">Designed by<br /><a href="" title="Jordittle Degign">Jordittle Design & Media</a><br />&copy; 2011-<?php echo date($y="Y"); ?></p>
        </div>
    </div>
</body>
</html>
<?php
mysql_free_result($rsSelectProjects);

mysql_free_result($rsProjectPictureList);

mysql_free_result($rsRemoveProjectSelect);
?>
