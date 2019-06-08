<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<link href="Resources/css/style.css" rel="stylesheet" type="text/css">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="Resources/nivo-slider/nivo-slider.css" type="text/css" />
<script src="Resources/nivo-slider/jquery.nivo.slider.pack.js" type="text/javascript"></script>
<link rel="stylesheet" href="Resources/nivo-slider/default/default.css" type="text/css" />

<script type="text/javascript">
$(window).load(function() {
    $('#slider').nivoSlider({
        effect:"sliceDownLeft",
        slices:15,
        boxCols:8,
        boxRows:4,
        animSpeed:500,
        pauseTime:5000,
        startSlide:0,
        directionNav:true,
        controlNav:false,
        controlNavThumbs:false,
        pauseOnHover:false,
        manualAdvance:false
    } );
});
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Affordable Car Mods :: Home</title>
<base href="http://affordablecarmods.com" />
</head>

<body>
	<div class="wrapper">
        <div id="header">
            <div id="innerheader">
                <div id="navigation-bar">
                    <a id="item-selected" href="index.php">
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
            <div class="slider-wrapper theme-default">
                <div class="ribbon"></div>
                    <div id="slider" class="nivoSlider">
                        <img src="Resources/Images/Slider/1.jpg" alt="" title="This is an example of a caption" height="500" />
                        <a href="http://dev7studios.com"><img src="Resources/Images/Slider/2.jpg" alt="" title="#htmlcaption" height="500" /></a>
                    </div>
                    <div id="htmlcaption" class="nivo-html-caption">
                        <strong>This</strong> is an example of a <em>HTML</em> caption with <a href="#">a link</a>.
                    </div>
                </div>
              	
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