<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- $Id -->
<html>
<head>
   <title>PHPlot Quickstart</title>
   <style type="text/css">
    <!--
    td { text-align:center; }
    .hdr { font-weight: bold; }
    -->
   </style>
</head>
<body>

<h2>PHPlot Quickstart</h2>

<p>
This page will test if you have GD and PHP set up to view images correctly. 
You should see at least one of the three images below. 
</p>

<center>
<table>
<tr><td class="hdr">PNG graphics</td></tr>
<tr><td>
<?php
    if (! function_exists("imagepng"))  
    	echo "PNG NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?file_format=png&which_title=YES_PNG_IS_ENABLED\" />";
    
?>
</td></tr>
<tr><td class="hdr">JPEG graphics</td></tr>
<tr><td>
<?php
    if (! function_exists("imagejpeg"))
        echo "JPEG NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?file_format=jpg&which_title=YES_JPG_IS_ENABLED\" />";
?>
</td></tr>
<tr><td class="hdr">GIF graphics</td></tr>
<tr><td>
<?php
    if (! function_exists("imagegif"))
	    echo "GIF NOT ENABLED";
    else
	    echo "<img src=\"inline_image.php?file_format=gif&which_title=YES_GIF_IS_ENABLED\" />";
?>
</td></tr>
</table>
</center>

</body>
</html>
