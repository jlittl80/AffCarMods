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

if (((isset($_POST['projectSelect'])) && ($_POST['projectSelect'] != "")) && ((isset($_POST['serviceSelect']) && ($_POST['serviceSelect'] != "") )) && ((isset($_POST['MM_removeTag']) && ($_POST['MM_removeTag'] == "removeTag") ))) {
  $deleteSQL = sprintf("DELETE FROM projectservices WHERE projectID=%s AND serviceID=%s",
                       GetSQLValueString($_POST['projectSelect'], "int"),
					   GetSQLValueString($_POST['serviceSelect'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($deleteSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $deleteGoTo = "tags.php?s=t";
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

if ((isset($_POST["MM_addTag"])) && ($_POST["MM_addTag"] == "addTag")) {
  $insertSQL = sprintf("INSERT INTO projectservices (serviceID, projectID) VALUES (%s, %s)",
                       GetSQLValueString($_POST['serviceSelect'], "int"),
                       GetSQLValueString($_POST['projectSelect'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));

  $insertGoTo = "tags.php?s=t";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_rsAddProjectSelect = "SELECT * FROM projects WHERE projectID NOT IN (SELECT projectID FROM projectservices GROUP BY projectservices.projectID HAVING COUNT(serviceID) = (SELECT COUNT(serviceID) FROM services))";
$rsAddProjectSelect = mysql_query($query_rsAddProjectSelect, $localhost) or die(mysql_error());
$row_rsAddProjectSelect = mysql_fetch_assoc($rsAddProjectSelect);
$totalRows_rsAddProjectSelect = mysql_num_rows($rsAddProjectSelect);

mysql_select_db($database_localhost, $localhost);
$query_rsRemoveProjectSelect = "SELECT * FROM projectservices JOIN projects ON projectservices.projectID = projects.projectID GROUP BY projectservices.projectID HAVING COUNT(serviceID) != 0";
$rsRemoveProjectSelect = mysql_query($query_rsRemoveProjectSelect, $localhost) or die(mysql_error());
$row_rsRemoveProjectSelect = mysql_fetch_assoc($rsRemoveProjectSelect);
$totalRows_rsRemoveProjectSelect = mysql_num_rows($rsRemoveProjectSelect);

mysql_select_db($database_localhost, $localhost);
$query_rsTagSelect = "SELECT projectservices.projectID, projects.carMake, projects.carModel, projects.carYear, projectservices.serviceID, services.serviceName FROM projectservices JOIN projects ON projectservices.projectID = projects.projectID JOIN services ON projectservices.serviceID = services.serviceID ORDER BY projectservices.projectID ASC, projectservices.serviceID ASC";
$rsTagSelect = mysql_query($query_rsTagSelect, $localhost) or die(mysql_error());
$row_rsTagSelect = mysql_fetch_assoc($rsTagSelect);
$totalRows_rsTagSelect = mysql_num_rows($rsTagSelect);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="../../Resources/css/style.css" rel="stylesheet" type="text/css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
<style type="text/css">
	form[name~=addTag] {
		float: left;
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
	form[name~=addTag] #projectDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=addTag] #projectDiv select {
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
	form[name~=addTag] #projectDiv a {
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
	form[name~=addTag] #projectDiv a:hover {
		background-color: #09F;
	}
	form[name~=addTag] #serviceDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		color: #FFF;
	}
	form[name~=addTag] #serviceDiv select {
		width: 170px;
		border: 0px;
		margin: 5px;
		height: 30px;
		padding: 0 5px 0 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	form[name~=addTag] #bottom {
		height:5px;
		background-color: #333;
		width: 200px;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
	}
	form[name~=addTag] input[type~=submit] {
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
	form[name~=addTag] input[type~=submit]:hover {
		background-color: #09F;
	}
	#tagList {
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
	#tagList table {
		margin-top: 5px;
		width: 100%;
		text-align: left;
		font-size:12px;
		background-color: #333;
		color: #FFF;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
	#tagList table #titleBar {
		font-size: 16px;
	}
	#tagList table tr:first-child td:first-child {
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		border-top-left-radius: 5px;
	}
	#tagList table tr:first-child td:last-child {
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		border-top-right-radius: 5px;
	}
	#tagList table tr:last-child td:first-child {
		-moz-border-radius-bottomleft: 5px;
		-webkit-border-bottom-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	#tagList table tr:last-child td:last-child {
		-moz-border-radius-bottomright: 5px;
		-webkit-border-bottom-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}
	form[name~=removeTag] {
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
	form[name~=removeTag] #projectDiv {
		width: 200px;
		padding: 5px 0 5px 0;
		background-color: #333;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #FFF;
		margin-top: 5px;
	}
	form[name~=removeTag] #projectDiv select {
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
	form[name~=removeTag] #projectDiv a {
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
	form[name~=removeTag] #projectDiv a:hover {
		background-color: #09F;
	}
	form[name~=removeTag] #serviceDiv {
		width: 200px;
		padding: 0 0 10px 0;
		background-color: #333;		
		color: #FFF;
	}
	form[name~=removeTag] #serviceDiv select {
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
	form[name~=removeTag] #bottom {
		height:5px;
		background-color: #333;
		width: 200px;
		-webkit-border-radius: 0 0 5px 5px;
		-moz-border-radius: 0 0 5px 5px;
		border-radius: 0 0 5px 5px;
	}
	form[name~=removeTag] input[type~=submit] {
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
	form[name~=removeTag] input[type~=submit]:hover {
		background-color: #09F;
	}
</style>

<script type="text/javascript">
function removeGetTags() {
	var projectID = $("#removeProjectSelect").val()
	url = "getData.php";
	var posting = $.post(url, {projectID:projectID, function:"removeGetTags"});
	
	posting.done(function(data) {
		$("#removeTag ").children("#serviceDiv").remove()
		$("#removeTag ").children("#removeTag").remove()
		$("#removeTag ").children("#projectDiv").after('<div id="serviceDiv">Service')
		$("#removeTag ").children("#serviceDiv").append(data)
		$("#removeTag ").children("#serviceDiv").append('</div>')
		$("#removeTag ").children("#bottom").after('<input name="removeTag" id="removeTag" type="submit" value="Remove" />')
	});
}
function addGetTags() {
	var projectID = $("#addProjectSelect").val()
	url = "getData.php";
	var posting = $.post(url, {projectID:projectID, function:"addGetTags"});
	
	posting.done(function(data) {
		$("#addTag").children("#serviceDiv").remove()
		$("#addTag").children("#addTag").remove()
		$("#addTag").children("#projectDiv").after('<div id="serviceDiv">Service')
		$("#addTag").children("#serviceDiv").append(data)
		$("#addTag").children("#serviceDiv").append('</div>')
		$("#addTag").children("#bottom").after('<input name="addTag" id="addTag" type="submit" value="Add" />')
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
                <a id="item" href="pictures.php" style="width:196px;">
                    Pictures
          </a>
                <a id="item-selected" href="tags.php">
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
  				<form action="<?php echo $editFormAction; ?>" name="addTag" id="addTag" method="POST">
                	Add Tag
           	  		<div id="projectDiv">
                    	Project
                		<select name="projectSelect" id="addProjectSelect" onchange="addGetTags()">
                		<?php
						do {  
						?>
							<option value="<?php echo $row_rsAddProjectSelect['projectID']?>"><?php echo "(".$row_rsAddProjectSelect['projectID'].") ".$row_rsAddProjectSelect['carMake']." ".$row_rsAddProjectSelect['carModel']." ".$row_rsAddProjectSelect['carYear']?></option>
						<?php
						} while ($row_rsAddProjectSelect = mysql_fetch_assoc($rsAddProjectSelect));
						  $rows = mysql_num_rows($rsAddProjectSelect);
						  if($rows > 0) {
							  mysql_data_seek($rsAddProjectSelect, 0);
							  $row_rsAddProjectSelect = mysql_fetch_assoc($rsAddProjectSelect);
						  }
						?>
						</select>
                        <a onclick="addGetTags()">Search</a>
					</div>
                    <div id="bottom"></div>
                  	<input type="hidden" name="MM_addTag" value="addTag" />
            	</form>
                <div id="tagList">
                	Tags
                    <table>
                    	<tr id="titleBar">
                        	<td width="10%">Project ID</td>
                            <td width="20%">Make</td>
                            <td width="20%">Model</td>
                            <td width="20%">Year</td>
                            <td width="10%">Service ID</td>
                            <td width="20%">Service</td>
                        </tr>
                    	<?php
                            do {  
                        ?>
                        <tr>
                        	<td><?php echo $row_rsTagSelect['projectID']?></td>
                            <td><?php echo $row_rsTagSelect['carMake']?></td>
                            <td><?php echo $row_rsTagSelect['carModel']?></td>
                            <td><?php echo $row_rsTagSelect['carYear']?></td>
                            <td><?php echo $row_rsTagSelect['serviceID']?></td>
                            <td><?php echo $row_rsTagSelect['serviceName']?></td>
                        </tr>
                        <?php
                            } while ($row_rsTagSelect = mysql_fetch_assoc($rsTagSelect));
                            $rows = mysql_num_rows($rsTagSelect);
                            if($rows > 0) {
                                mysql_data_seek($rsTagSelect, 0);
                                $row_rsTagSelect = mysql_fetch_assoc($rsTagSelect);
                            }
                        ?>
                    </table>
                </div>
                <form name="removeTag" id="removeTag" method="POST">
                	Remove Tag
                    <div id="projectDiv">
                    	Project
                    	<select name="projectSelect" id="removeProjectSelect" onchange="removeGetTags()">
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
                        <a onclick="removeGetTags()">Search</a>
                    </div>
                    
                    <div id="bottom"></div>
                  	<input type="hidden" name="MM_removeTag" value="removeTag" />
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
mysql_free_result($rsAddProjectSelect);

mysql_free_result($rsRemoveProjectSelect);

mysql_free_result($rsTagSelect);
?>
