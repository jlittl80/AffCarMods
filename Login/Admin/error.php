<h1>The most friendly error page I cbf making, enjoy</h1>
Also, this will only show the FIRST error that heppens in the script. That means, if there is another one after the one you are seeing, it wont say because that part of the script never ran.
<br>
<h3>FYI if you want me to fix a problem, SCREEN SHOT THIS</h3>
<h2>ERROR</h2>
<?php
	echo $_GET['error'];
?>
<h2>I CAME FROM</h2>
<?php
	echo $_SERVER['HTTP_REFERER'];
?>
<br>
<br>
<br>
<a href="index.php" title="Return">Return to index</a>