<?php require_once('../Connections/localhost.php'); ?>
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

mysql_select_db($database_localhost, $localhost);
$query_rsGetServices = "SELECT * FROM services";
$rsGetServices = mysql_query($query_rsGetServices, $localhost) or die(mysql_error());
$row_rsGetServices = mysql_fetch_assoc($rsGetServices);
$totalRows_rsGetServices = mysql_num_rows($rsGetServices);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="../Resources/css/style.css" rel="stylesheet" type="text/css">
<style>
body {
    background-color: #f7f7f7;
}

#v-nav {
    height: 100%;
    margin: auto;
    color: #333;
    font: 12px/18px "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
}

#v-nav >ul {
    float: left;
    width: 210px;
    display: block;
    position: relative;
    top: 0;
    border: 1px solid #DDD;
    border-right-width: 0;
    margin: auto 0 !important;
    padding:0;
}

#v-nav >ul >li {
    width: 180px;
    list-style-type: none;
    display: block;
    text-shadow: 0px 1px 1px #F2F1F0;
    font-size: 1.11em;
    position: relative;
    border-right-width: 0;
    border-bottom: 1px solid #DDD;
    margin: auto;
    padding: 10px 15px !important;
    background: whiteSmoke; /* Old browsers */
    background: -moz-linear-gradient(top, #ffffff 0%, #f2f2f2 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffffff), color-stop(100%, #f2f2f2)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #ffffff 0%, #f2f2f2 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #ffffff 0%, #f2f2f2 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top, #ffffff 0%, #f2f2f2 100%); /* IE10+ */
    background: linear-gradient(top, #ffffff 0%, #f2f2f2 100%); /* W3C */
}

#v-nav >ul >li.current {
    color: black;
    border-right: none;
    z-index: 10;
    background: white !important;
    position: relative;
    moz-box-shadow: inset 0 0 35px 5px #fafbfd;
    -webkit-box-shadow: inset 0 0 35px 5px #fafbfd;
    box-shadow: inset 0 0 35px 5px #fafbfd;
}

#v-nav >ul >li.first.current {
    border-bottom: 1px solid #DDD;
}

#v-nav >ul >li.last {
    border-bottom: none;
}

#v-nav >div.tab-content {
    margin-left: 210px;
    border: 1px solid #ddd;
    background-color: #FFF;
    min-height: 400px;
    position: relative;
    z-index: 9;
    padding: 12px;
    moz-box-shadow: inset 0 0 35px 5px #fafbfd;
    -webkit-box-shadow: inset 0 0 35px 5px #fafbfd;
    box-shadow: inset 0 0 35px 5px #fafbfd;
    display: none;
    padding: 25px;
}

#v-nav >div.tab-content >h4 {
    font-size: 1.2em;
    color: Black;
    padding-top: 5px;
}
#v-nav >div.tab-content >h5 {
	font-weight:normal;
    font-size: 0.8em;
    color: Black;
    text-shadow: 0px 1px 1px #F2F1F0;
    border-bottom: 1px dotted #EEEDED;
    padding-top: 5px;
    padding-bottom: 5px;
	margin-bottom: 10px;
}

#projectContainer {
	background-color: #09F;
	margin-top: 20px;
	width:740px;
	height:100px;
	background-position:center;
	-webkit-border-radius: 5px 5px 5px 5px;	-moz-border-radius: 5px 5px 5px 5px; border-radius: 5px 5px 5px 5px;
}
#projectContainer a { 
	text-decoration: none;
}
#projectContainer h1 {
	padding: 20px;
	color: #FFF;
	font-family: 'Open Sans', sans-serif;
	font-size: 2em;
	font-weight:lighter;
	line-height: 60px;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://benalman.com/code/projects/jquery-hashchange/jquery.ba-hashchange.js"></script>
<script type="text/javascript">
$(function () {
    var items = $('#v-nav>ul>li').each(function () {
        $(this).click(function () {
            //remove previous class and add it to clicked tab
            items.removeClass('current');
            $(this).addClass('current');   

            $('#v-nav>div.tab-content').hide().eq(items.index($(this))).show();
            window.location.hash = $(this).attr('tab');
        });
    });

    if (location.hash) {
        showTab(location.hash);
    }
    else {
        showTab("tab1");
    }

    function showTab(tab) {
        $("#v-nav ul li[tab=" + tab + "]").click();
    }

    // Bind the event hashchange, using jquery-hashchange-plugin
    $(window).hashchange(function () {
        showTab(location.hash.replace("#", ""));
    })

    // Trigger the event hashchange on page load, using jquery-hashchange-plugin
    $(window).hashchange();
});
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Affordable Car Mods :: Services</title>
<base href="http://affordablecarmods.com" />
</head>

<body>
	<div class="wrapper">
        <div id="header">
            <div id="innerheader">
                <div id="navigation-bar">
                    <a id="item" href="index.php">
                        Home
                    </a>
                    <a id="item-selected" href="Services/index.php">
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
              	<div id="v-nav">
                <?php
				if ($totalRows_rsGetServices > 0) {
				?>
            	<ul>
                	<?php 
					$temp = 1;
					do { 
						if($temp == 1) {
					?>
                    <li tab="tab<?php echo $temp; ?>" class="first current"><?php echo $row_rsGetServices['serviceName']; ?></li>
                    <?php							
						} elseif($temp == $totalRows_rsGetServices) {
					?>
                    <li tab="tab<?php echo $temp; ?>" class="last"><?php echo $row_rsGetServices['serviceName']; ?></li>
                    <?php
						} else {
					?>
                    <li tab="tab<?php echo $temp; ?>"><?php echo $row_rsGetServices['serviceName']; ?></li>
                    <?php
						}
						$temp++;
					?>
					<?php 
					} while ($row_rsGetServices = mysql_fetch_assoc($rsGetServices));
					mysql_data_seek($rsGetServices, 0);
					$row_rsGetServices = mysql_fetch_assoc($rsGetServices);
					?>
                </ul>
                <?php
				do { 
				?>
                <div class="tab-content">
                    <h4><?php echo $row_rsGetServices['serviceName']; ?></h4>
                    <h5><?php echo $row_rsGetServices['serviceDescription']; ?></h5>
                    Check out projects that have had this service.
                    <?php
					//Selects projects with the service
					mysql_select_db($database_localhost, $localhost);
					$query_rsGetProjectService = sprintf("SELECT * FROM projectservices JOIN projects ON projectservices.projectID = projects.projectID WHERE projectservices.serviceID = %s", GetSQLValueString($row_rsGetServices['serviceID'], "int"));
					$rsGetProjectService = mysql_query($query_rsGetProjectService, $localhost) or die(mysql_error());
					$row_rsGetProjectService = mysql_fetch_assoc($rsGetProjectService);
					$totalRows_rsGetProjectService = mysql_num_rows($rsGetProjectService);
					
					if ($totalRows_rsGetProjectService > 0) {
						do {
							mysql_select_db($database_localhost, $localhost);
							$query_rsProjectPicture = sprintf("SELECT * FROM projectpictures WHERE projectID = %s AND pictureID = 'default.jpg'", GetSQLValueString($row_rsGetProjectService['projectID'], "int"));
							$rsProjectPicture = mysql_query($query_rsProjectPicture, $localhost) or die(mysql_error());
							$row_rsProjectPicture = mysql_fetch_assoc($rsProjectPicture);
							$totalRows_rsProjectPicture = mysql_num_rows($rsProjectPicture);
					?>
                    <div id="projectContainer" style="background-image:url(<?php if($totalRows_rsProjectPicture > 0) { echo "../Resources/Images/Projects/".$row_rsGetProjectService['projectID']."/default.jpg"; } else { echo "../Resources/Images/default.jpg"; } ?>);">
                        <a href="../Projects/index.php#project<?php echo $row_rsGetProjectService['projectID'] ?>"><h1><?php echo $row_rsGetProjectService['carMake']." ".$row_rsGetProjectService['carModel']." ".$row_rsGetProjectService['carYear']; ?></h1></a>
                    </div>
					<?php
						} while ($row_rsGetProjectService = mysql_fetch_assoc($rsGetProjectService));
					}
					?>
                </div>
                <?php
				} while ($row_rsGetServices = mysql_fetch_assoc($rsGetServices));
				?>
                <?php
				}
				?>
            </div>

			<?php do { ?>
				<?php echo $row_rsGetServices['serviceID']; ?>
				<?php echo $row_rsGetServices['serviceName']; ?>
				<?php echo $row_rsGetServices['serviceDescription']; ?>
			<?php } while ($row_rsGetServices = mysql_fetch_assoc($rsGetServices)); ?>
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