<?php
if (!isset($_SESSION)) {
  session_start();
}
if (!(isset($_SESSION['MM_userID']))) {
	$MM_restrictGoTo = "../index.php";
	header("Location: ". $MM_restrictGoTo); 
}
?>
<?php require_once('../../Connections/localhost.php'); ?>
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
	if(isset($_POST['function'])) {
		if($_POST['function'] == "removeGetTags") {
			if((isset($_POST['projectID'])) && ($_POST['projectID'] != "")) {
				mysql_select_db($database_localhost, $localhost);
				$query_rsServiceSelect = sprintf("SELECT services.serviceID, services.serviceName FROM projectservices JOIN projects ON projectservices.projectID = projects.projectID JOIN services ON projectservices.serviceID = services.serviceID WHERE projectservices.projectID = %s ORDER BY projectservices.projectID ASC, projectservices.serviceID ASC", GetSQLValueString($_POST['projectID'], "int"));
				$rsServiceSelect = mysql_query($query_rsServiceSelect, $localhost) or die(header(sprintf("Location: %s", "error.php?error=".mysql_error())));
				$row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect);
				$totalRows_rsServiceSelect = mysql_num_rows($rsServiceSelect);
?>
<select name="serviceSelect">
	<?php
	do {  
	?>
  	<option value="<?php echo $row_rsServiceSelect['serviceID']?>"><?php echo "(".$row_rsServiceSelect['serviceID'].") ".$row_rsServiceSelect['serviceName']?></option>
 	<?php
	} while ($row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect));
  	$rows = mysql_num_rows($rsServiceSelect);
  	if($rows > 0) {
    	mysql_data_seek($rsServiceSelect, 0);
	  	$row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect);
  	}
?>
</select>
<?php
			}
		} elseif($_POST['function'] == "addGetTags") {
			if((isset($_POST['projectID'])) && ($_POST['projectID'] != "")) {
				mysql_select_db($database_localhost, $localhost);
				$query_rsServiceSelect = sprintf("SELECT * FROM services WHERE services.serviceID NOT IN ( SELECT projectservices.serviceID FROM projectservices JOIN services ON projectservices.serviceID = services.serviceID WHERE projectservices.projectID = %s ORDER BY projectservices.projectID ASC, projectservices.serviceID ASC) ORDER BY services.serviceID ASC", GetSQLValueString($_POST['projectID'], "int"));
				$rsServiceSelect = mysql_query($query_rsServiceSelect, $localhost) or die(mysql_error());
				$row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect);
				$totalRows_rsServiceSelect = mysql_num_rows($rsServiceSelect);
?>
<select name="serviceSelect">
<?php
if($totalRows_rsServiceSelect != 0) {
do {  
?>
	<option value="<?php echo $row_rsServiceSelect['serviceID']?>"><?php echo "(".$row_rsServiceSelect['serviceID'].") ".$row_rsServiceSelect['serviceName']?></option>
<?php
} while ($row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect));
$rows = mysql_num_rows($rsServiceSelect);
if($rows > 0) {
	mysql_data_seek($rsServiceSelect, 0);
	$row_rsServiceSelect = mysql_fetch_assoc($rsServiceSelect);
}
}
?>
</select>
<?php
			}
		} elseif($_POST['function'] == "removeGetPictures") {
			if((isset($_POST['projectID'])) && ($_POST['projectID'] != "")) {
				mysql_select_db($database_localhost, $localhost);
				$query_rsPictureSelect = sprintf("SELECT * FROM projectpictures WHERE projectID = %s", GetSQLValueString($_POST['projectID'], "int"));
				$rsPictureSelect = mysql_query($query_rsPictureSelect, $localhost) or die(mysql_error());
				$row_rsPictureSelect = mysql_fetch_assoc($rsPictureSelect);
				$totalRows_rsPictureSelect = mysql_num_rows($rsPictureSelect);
?>
<select name="pictureSelect">
<?php
if($totalRows_rsPictureSelect != 0) {
do {  
?>
	<option value="<?php echo $row_rsPictureSelect['pictureID']?>"><?php echo $row_rsPictureSelect['pictureID']?></option>
<?php
} while ($row_rsPictureSelect = mysql_fetch_assoc($rsPictureSelect));
$rows = mysql_num_rows($rsPictureSelect);
if($rows > 0) {
	mysql_data_seek($rsPictureSelect, 0);
	$row_rsPictureSelect = mysql_fetch_assoc($rsPictureSelect);
}
}
?>
</select>
<?php
			}
		}
	}
?>