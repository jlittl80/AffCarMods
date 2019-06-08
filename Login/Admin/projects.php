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

if ((isset($_POST['projectSelect'])) && ($_POST['projectSelect'] != "") && (isset($_POST['MM_projectDelete'])) && ($_POST["MM_projectDelete"] == "removeProject")) {
  $deleteSQL = sprintf("DELETE FROM projects WHERE projectID=%s",
                       GetSQLValueString($_POST['projectSelect'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($deleteSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $deleteGoTo = "projects.php?s=t";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_projectInsert"])) && ($_POST["MM_projectInsert"] == "addProject")) {
  $insertSQL = sprintf("INSERT INTO projects (carMake, carModel, carYear, `projectDescription`) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['carMake'], "text"),
                       GetSQLValueString($_POST['carModel'], "text"),
                       GetSQLValueString($_POST['carYear'], "int"),
                       GetSQLValueString($_POST['description'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $insertGoTo = "projects.php?s=t";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_rsSelectProjects = "SELECT * FROM projects";
$rsSelectProjects = mysql_query($query_rsSelectProjects, $localhost) or die(mysql_error());
$row_rsSelectProjects = mysql_fetch_assoc($rsSelectProjects);
$totalRows_rsSelectProjects = mysql_num_rows($rsSelectProjects);

mysql_select_db($database_localhost, $localhost);
$query_rsProjectList = "SELECT * FROM projects ORDER BY projectID ASC";
$rsProjectList = mysql_query($query_rsProjectList, $localhost) or die(mysql_error());
$row_rsProjectList = mysql_fetch_assoc($rsProjectList);
$totalRows_rsProjectList = mysql_num_rows($rsProjectList);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="../../Resources/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	form[name~=addProject] {
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
	form[name~=addProject] #carMakeDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=addProject] #carMakeDiv input[type~=text] {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addProject] #carModelDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		color: #FFF;
	}
	form[name~=addProject] #carModelDiv input[type~=text] {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addProject] #carYearDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		color: #FFF;
	}
	form[name~=addProject] #carYearDiv input[type~=text] {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addProject] #desriptionDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
		color: #FFF;
	}
	form[name~=addProject] #desriptionDiv textarea {
		width: 170px;
		max-width: 180px;
		height: 200px;
		min-height: 200px;
		resize:none;
		border: 0px;
		margin: 5px;
		height: 25px;
		padding: 3px 5px 3px 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addProject] input[type~=submit] {
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
	form[name~=addProject] input[type~=submit]:hover {
		background-color: #09F;
	}
	
	#projectList {
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
	#projectList table {
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
	#projectList table #titleBar {
		font-size: 16px;
	}
	#projectList table tr:first-child td:first-child {
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		border-top-left-radius: 5px;
	}
	#projectList table tr:first-child td:last-child {
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		border-top-right-radius: 5px;
	}
	#projectList table tr:last-child td:first-child {
		-moz-border-radius-bottomleft: 5px;
		-webkit-border-bottom-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	#projectList table tr:last-child td:last-child {
		-moz-border-radius-bottomright: 5px;
		-webkit-border-bottom-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}
	
	form[name~=removeProject] {
		float: right;
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
	form[name~=removeProject] #projectNameDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=removeProject] #projectNameDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=removeProject] input[type~=submit] {
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
	form[name~=removeProject] input[type~=submit]:hover {
		background-color: #09F;
	}
</style>

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
                <a id="item-selected" href="projects.php">
                    Projects
          </a>
                <a id="item" href="pictures.php" style="width:196px;">
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
                <form name="addProject" action="<?php echo $editFormAction; ?>" method="POST">
                	Add Project
                	<div id="carMakeDiv">
                    	Car Make
                		<input name="carMake" type="text" autocomplete="off" />
                    </div>
                    <div id="carModelDiv">
                    	Car Model
                        <input name="carModel" type="text" autocomplete="off" />
                    </div>
                    <div id="carYearDiv">
                    	Car Year
                        <input name="carYear" type="text" autocomplete="off" />
                    </div>
                    <div id="desriptionDiv">
                    	Description
                    	<textarea name="description" rows="5" cols="100"></textarea>
                    </div>
                    <input name="addProject" type="submit" value="Add" />
                    <input type="hidden" name="MM_projectInsert" value="addProject" />
                </form>
                <div id="projectList">
                	Projects
                    <table>
                    	<tr id="titleBar">
                        	<td width="10%">ID</td>
                            <td width="20%">Make</td>
                            <td width="20%">Model</td>
                            <td width="20%">Year</td>
                            <td width="30%">Description</td>
                        </tr>
                    	<?php
                            do {  
                        ?>
                        <tr>
                        	<td><?php echo $row_rsProjectList['projectID']?></td>
                            <td><?php echo $row_rsProjectList['carMake']?></td>
                            <td><?php echo $row_rsProjectList['carModel']?></td>
                            <td><?php echo $row_rsProjectList['carYear']?></td>
                            <td><?php echo $row_rsProjectList['projectDescription']?></td>
                        </tr>
                        <?php
                            } while ($row_rsProjectList = mysql_fetch_assoc($rsProjectList));
                            $rows = mysql_num_rows($rsProjectList);
                            if($rows > 0) {
                                mysql_data_seek($rsProjectList, 0);
                                $row_rsProjectList = mysql_fetch_assoc($rsProjectList);
                            }
                        ?>
                    </table>
                </div>
                <form name="removeProject" method="POST">
                	Remove Project
           		  	<div id="projectNameDiv">
                    	Project
                        <select name="projectSelect">
                         	<?php
							do {  
							?>
                       	 	<option value="<?php echo $row_rsSelectProjects['projectID']?>"><?php echo $row_rsSelectProjects['projectID']?></option>
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
                  	<input name="removeProject" type="submit"  value="Remove" />
               		<input type="hidden" name="MM_projectDelete" value="removeProject" />
       		 	</form>
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

mysql_free_result($rsProjectList);
?>
