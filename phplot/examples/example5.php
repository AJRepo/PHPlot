<HTML>
<BODY>
<FORM ACTION="test1.php" METHOD="POST">
NOTE! For this example script, if you change a parameter you have to RELOAD the GRAPH generated.
<H2>Bar Details</H2>
Mandatory Settings: <br>
<INPUT TYPE="TEXT" NAME="xsize_in" VALUE="600"> Width of Graph in Pixels<br>
<INPUT TYPE="TEXT" NAME="ysize_in" VALUE="400"> Height of Graph in Pixels<br>
<p>
Data Type: linear-linear-error</A>//
<p>
Data: (linear-linear)<br>
<INPUT TYPE="hidden" NAME="which_data_type" VALUE="linear-linear-error">
<TABLE border=1>
<TR><TD>Title (data label)</TD><TD>Xdata</TD><TD>Ydata 1</TD><TD>Error +</TD><TD>Error -</TD></TR>
</TABLE>
<p>
Chart Type: <SELECT NAME="which_plot_type">
<OPTION VALUE="points">Points
<OPTION VALUE="lines">Lines
<OPTION VALUE="linepoints">Line and Points
</SELECT>

Error Bar Type:<SELECT NAME="which_error_type">
<OPTION VALUE="line">Line
<OPTION VALUE="tee">Tee
</SELECT>
<p>
Optional Settings: (Leave blank for automatic calculated values)<br>
<UL>
<SELECT NAME="which_dot">
	<OPTION>halfline
	<OPTION>diamond
	<OPTION>rect
	<OPTION>circle
	<OPTION>triangle
	<OPTION>dot
	<OPTION>line
</SELECT> Select Point Type 
<br>
<INPUT TYPE="TEXT" NAME="maxy_in" VALUE=""> Maximum Height of Graph in Y Axis Units </br>
<INPUT TYPE="TEXT" NAME="miny_in" VALUE=""> Minimum Height of Graph in Y Axis Units </br>
<INPUT TYPE="TEXT" NAME="ylbl" VALUE="Share Price"> Y Axis Label<br> 
<INPUT TYPE="TEXT" NAME="xlbl" VALUE="Day"> X Axis Label<br> 
<INPUT TYPE="TEXT" NAME="title" VALUE="This is a title"> Title<br> 
<INPUT TYPE="TEXT" NAME="which_vti" VALUE=""> Vertical Tick Increment (blank for auto)<br>
<INPUT TYPE="TEXT" NAME="which_hti" VALUE="1"> Horizontal Tick Increment (blank for auto)<br>
</UL>
<INPUT TYPE="SUBMIT">

<A HREF="http://www.jeo.net/php/">
<p>More PHP code and examples</A> by Afan Ottenheimer of JEONET
</BODY></HTML>
