<HTML>
<BODY>
<FORM ACTION="create_chart.php" METHOD="POST">
NOTE! For this example script, if you change a parameter you have to RELOAD the GRAPH generated.
<H2>Bar Details</H2>
Mandatory Settings: <br>
<INPUT TYPE="TEXT" NAME="XSIZE_in" VALUE="600"> Width of Graph in Pixels<br>
<INPUT TYPE="TEXT" NAME="YSIZE_in" VALUE="400"> Height of Graph in Pixels<br>
<p>
<?php
if ($which_data_type=="text-linear") { 
	include("data_sample1.php"); 
} elseif ($which_data_type=="linear-linear") { 
	include("data_sample2.php");
} elseif ($which_data_type=="linear-linear-error"){ 
	include("data_sample3.php");
} elseif ($which_data_type=="function"){ 
	include("data_sample4.php");
} else { 
	include("data_sample1.php");
}
?>
<p>
Optional Settings: (Leave blank for automatic calculated values)<br>
<UL>
<SELECT NAME="which_dot">
	<OPTION>diamond
	<OPTION>rect
	<OPTION>circle
	<OPTION>triangle
	<OPTION>dot
	<OPTION>line
	<OPTION>halfline
</SELECT> Select Point Type 
<br>
<INPUT TYPE="TEXT" NAME="maxy_in" VALUE=""> Maximum Height of Graph in Y Axis Units </br>
<INPUT TYPE="TEXT" NAME="miny_in" VALUE=""> Minimum Height of Graph in Y Axis Units </br>
<INPUT TYPE="TEXT" NAME="ylbl" VALUE="Revenue in Millions"> Y Axis Label<br> 
<INPUT TYPE="TEXT" NAME="xlbl" VALUE="Years"> X Axis Label<br> 
<INPUT TYPE="TEXT" NAME="title" VALUE="This is a title"> Title<br> 
<INPUT TYPE="TEXT" NAME="which_vti" VALUE=""> Veritcal Tick Increment (blank for auto)<br>
<INPUT TYPE="TEXT" NAME="which_hti" VALUE="1"> Horizontal Tick Increment (blank for auto)<br>
<INPUT TYPE="TEXT" NAME="which_xap" VALUE="1"> X Axis Position (blank for 0)<br>
</UL>
<INPUT TYPE="SUBMIT">

<A HREF="http://www.jeo.net/php/">
<p>More PHP code and examples</A> by Afan Ottenheimer of JEONET
</BODY></HTML>
