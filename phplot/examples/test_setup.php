<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<TITLE>PHPlot Quickstart</TITLE>
</HEAD>
<BODY>

<h2>PHPlot Quickstart</h2>

This page will test to see if you have GD and PHP set up to 
view images correctly. You should see at least one
of the three images below. 
<p>
<?php
/* *************************
error_reporting(4);
$im_test = ImageCreate(2,2);
$test = (ImagePng($im_test)) ;
ImageDestroy($im_test);
exit;
**************************** */
?>

<p>
<TABLE border=1>
<TR><TD>Test to see if PNG graphs work</TD></TR>
<TR><TD>
<?php
$im_test = ImageCreate(1,1);
echo "<!--";
if (! ImagePng($im_test) ) { 
	//In php3 this is a fatal error, in php4 it is a warning. 
	echo "-->";
	echo "PNG NOT ENABLED";
} else { 
	echo "-->";
	?>
	<IMG SRC="inline_image.php?file_format=png&which_title=YES_PNG_IS_ENABLED"></TD></TR>
	<?php
}
ImageDestroy($im_test);
?>
</TABLE>
<p>
<TABLE border=1>
<TR><TD>Test to see if JPEG graphs work</TD></TR>
<TR><TD>
<?php
$im_test = ImageCreate(1,1);
if (! ImageJPEG($im_test) ) { 
	echo "JPEG NOT ENABLED";
} else { 
	?>
	<IMG SRC="inline_image.php?file_format=jpg&which_title=YES_JPG_IS_ENABLED"></TD></TR>
	<?php
}
ImageDestroy($im_test);
?>
</TABLE>

<TABLE border=1>
<TR><TD>Test to see if GIF graphs work</TD></TR>
<TR><TD>
<?php
$im_test = ImageCreate(1,1);
echo "<!--";
if (! ImageGif($im_test)) { 
	echo "-->";
	echo "GIF NOT ENABLED<br>";
} else { 
	echo "-->";
	?>
	<IMG SRC="inline_image.php?file_format=gif&which_title=YES_GIF_IS_ENABLED"></TD></TR>
	<?php
}
ImageDestroy($im_test);
?>
</TABLE>

</body>
</html>
