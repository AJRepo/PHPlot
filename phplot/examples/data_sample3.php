Data Type: <A HREF="format_chart.php?which_data_type=text-linear">text-linear</A>//
<A HREF="format_chart.php?which_data_type=linear-linear">linear-linear</A>//
<A HREF="format_chart.php?which_data_type=function">function</A>//
<A HREF="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</A><br>
<p>
Data set as X, Y, Y+, Y-
<?php
//linear-linear-error
		$data = array(
			array("label 0", 0, 1, .5, .1 ), 	
			array("label 1", 2, 5, .5, .4 ),
			array("label 2", 3, 2, .1, .1 ),
			array("label 3", 4, 5, .5, .5 ),
			array("label 4", 5, 1, .1, .1 )
		);
?>
Data: (linear-linear)<br>
<INPUT TYPE="hidden" NAME="which_data_type" VALUE="linear-linear-error">
<TABLE border=1>
<TR><TD>Title (data lablel)</TD><TD>Xdata</TD><TD>Ydata 1</TD><TD>Error +</TD><TD>Error -</TD></TR>
<?php
	for ($i=0; $i<5; $i++) {
?>
<tr>
<TD>
<input TYPE="text" NAME="data_row0[<?php echo $i?>]" 
	VALUE="<?php echo $data[$i][0]?>" >
</TD><TD>
<input TYPE="text" NAME="data_row1[<?php echo $i?>]" 
	VALUE="<?php echo $data[$i][1]?>" SIZE="3">
</TD><TD>
<input TYPE="text" NAME="data_row2[<?php echo $i?>]"
	VALUE="<?php echo $data[$i][2]?>" SIZE="3">
</TD><TD>
<input TYPE="text" NAME="data_row3[<?php echo $i?>]"
	VALUE="<?php echo $data[$i][3]?>" SIZE="3">
<br>
</TD><TD>
<input TYPE="text" NAME="data_row4[<?php echo $i?>]"
	VALUE="<?php echo $data[$i][4]?>" SIZE="3">
<br>
</TD><TD>
</TR>
<?php
	}
?>
</TABLE>
<p>
Chart Type: <SELECT NAME="which_plot_type">
<OPTION VALUE="lines">Lines
<OPTION VALUE="linepoints">Line and Points
<OPTION VALUE="points">Points
</SELECT>

Error Bar Type:<SELECT NAME="which_error_type">
<OPTION VALUE="tee">Tee
<OPTION VALUE="line">Line
</SELECT>
