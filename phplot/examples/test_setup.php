<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- $Id$ -->
<html>
<head>
   <title>PHPlot graphic formats test</title>
   <link type="text/css" rel="stylesheet" href="../doc/style.css" />
</head>
<body>

<?php include("nav.html"); ?>

<h2>PHPlot graphic formats test</h2>

<p>
This page will test which graphic formats are supported by the version of GD 
linked into PHP. You should see at least one of the four images below. 
</p>

<center>
<table>
<tr><td class="hdr">PNG graphics</td></tr>
<tr><td>
<?php
    if (! imagetypes() & IMG_PNG )  
    	echo "PNG NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?which_format=png&which_title=YES_PNG_IS_ENABLED\" />";
    
?>
</td></tr>
<tr><td class="hdr">JPEG graphics</td></tr>
<tr><td>
<?php
    if (! imagetypes() & IMG_JPG )
        echo "JPEG NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?which_format=jpg&which_title=YES_JPG_IS_ENABLED\" />";
?>
</td></tr>
<tr><td class="hdr">GIF graphics</td></tr>
<tr><td>
<?php
    if (! imagetypes() & IMG_GIF)
	    echo "GIF NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?which_format=gif&which_title=YES_GIF_IS_ENABLED\" />";
?>
</td></tr>
<tr><td class="hdr">BMP graphics</td></tr>
<tr><td>
<?php
    if (! imagetypes() & IMG_WBMP)
	    echo "BMP NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?which_format=wbmp&which_title=YES_WBMP_IS_ENABLED\" />";
?>
</td></tr>
</table>
</center>

</body>
</html>
