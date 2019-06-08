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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addService")) {
  $insertSQL = sprintf("INSERT INTO services (serviceName, `serviceDescription`) VALUES (%s, %s)",
                       GetSQLValueString($_POST['serviceName'], "text"),
                       GetSQLValueString($_POST['description'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $insertGoTo = "index.php?s=t";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST['services'])) && ($_POST['services'] != "") && (isset($_POST['MM_delete']))) {
  $deleteSQL = sprintf("DELETE FROM services WHERE serviceID=%s",
                       GetSQLValueString($_POST['services'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($deleteSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $deleteGoTo = "index.php?s=t";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_rsSelectServices = "SELECT * FROM services ORDER BY serviceID ASC";
$rsSelectServices = mysql_query($query_rsSelectServices, $localhost) or die(mysql_error());
$row_rsSelectServices = mysql_fetch_assoc($rsSelectServices);
$totalRows_rsSelectServices = mysql_num_rows($rsSelectServices);

mysql_select_db($database_localhost, $localhost);
$query_rsServiceList = "SELECT * FROM services";
$rsServiceList = mysql_query($query_rsServiceList, $localhost) or die(mysql_error());
$row_rsServiceList = mysql_fetch_assoc($rsServiceList);
$totalRows_rsServiceList = mysql_num_rows($rsServiceList);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="../../Resources/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	form[name~=addService] {
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
	form[name~=addService] #serviceNameDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=addService] #serviceNameDiv input[type~=text] {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addService] #serviceDesriptionDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
		color: #FFF;
	}
	form[name~=addService] #serviceDesriptionDiv textarea {
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
	form[name~=addService] input[type~=submit] {
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
	form[name~=addService] input[type~=submit]:hover {
		background-color: #09F;
	}
	#serviceList {
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
	#serviceList table {
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
	#serviceList table #titleBar {
		font-size: 16px;
	}
	#serviceList table tr:first-child td:first-child {
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		border-top-left-radius: 5px;
	}
	#serviceList table tr:first-child td:last-child {
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		border-top-right-radius: 5px;
	}
	#serviceList table tr:last-child td:first-child {
		-moz-border-radius-bottomleft: 5px;
		-webkit-border-bottom-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	#serviceList table tr:last-child td:last-child {
		-moz-border-radius-bottomright: 5px;
		-webkit-border-bottom-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}
	
	form[name~=removeService] {
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
	form[name~=removeService] #serviceNameDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		border-radius: 5px 5px 5px 5px;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=removeService] #serviceNameDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=removeService] input[type~=submit] {
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
	form[name~=removeService] input[type~=submit]:hover {
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
                <a id="item-selected" href="index.php">
                    Services
          </a>
                <a id="item" href="projects.php">
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
  				<form name="addService" method="POST" action="<?php echo $editFormAction; ?>">
                	Add Service
                    <div id="serviceNameDiv">
                    	Service Name
                    	<input name="serviceName" type="text" autocomplete="off" />
                    </div>
                    <div id="serviceDesriptionDiv">
                    	Description
                    	<textarea name="description" rows="5" cols="100"></textarea>
                    </div>
                    <input name="insertService" type="submit" value="Add" />
                  	<input type="hidden" name="MM_insert" value="addService" />
                </form>
                <div id="serviceList">
                	Services
                    <table>
                    	<tr id="titleBar">
                        	<td width="10%">ID</td>
                            <td width="30%">Service Name</td>
                            <td width="60%">Description</td>
                        </tr>
                    	<?php
                            do {  
                        ?>
                        <tr>
                        	<td><?php echo $row_rsServiceList['serviceID']?></td>
                            <td><?php echo $row_rsServiceList['serviceName']?></td>
                            <td><?php echo $row_rsServiceList['serviceDescription']?></td>
                        </tr>
                        <?php
                            } while ($row_rsServiceList = mysql_fetch_assoc($rsServiceList));
                            $rows = mysql_num_rows($rsServiceList);
                            if($rows > 0) {
                                mysql_data_seek($rsServiceList, 0);
                                $row_rsServiceList = mysql_fetch_assoc($rsServiceList);
                            }
                        ?>
                    </table>
                </div>
          		<form name="removeService" method="POST" action="<?php echo $editFormAction; ?>">
                	Remove Service
                    <div id="serviceNameDiv">
                    	Service
                        <select name="services">
                        <?php
                            do {  
                        ?>
                        <option value="<?php echo $row_rsSelectServices['serviceID']?>"><?php echo $row_rsSelectServices['serviceID']?></option>
                        <?php
                            } while ($row_rsSelectServices = mysql_fetch_assoc($rsSelectServices));
                            $rows = mysql_num_rows($rsSelectServices);
                            if($rows > 0) {
                                mysql_data_seek($rsSelectServices, 0);
                                $row_rsSelectServices = mysql_fetch_assoc($rsSelectServices);
                            }
                        ?>
                        </select>
                    </div>
                	<input name="removeService" type="submit" value="Remove" />
                    <input type="hidden" name="MM_delete" value="addService" />
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
mysql_free_result($rsSelectServices);

mysql_free_result($rsServiceList);
?>
