Data Type: <A HREF="format_chart.php?which_data_type=text-linear">text-linear</A>//
<A HREF="format_chart.php?which_data_type=linear-linear">linear-linear</A>//
<A HREF="format_chart.php?which_data_type=function">function</A>//
<A HREF="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</A><br>
<p>
<?php 
//linear-linear as just data
		$data = array(
			array("label 0", 0, 2, 5 ), 	
			array("label 1", 2, 3, 4 ),
			array("label 2", 3, 4, 3 ),
			array("label 3", 4.5, 5, 2 ),
			array("label 4", 5, 6, 1 )
		);
?>
Data: (linear-linear)<br>
<INPUT TYPE="hidden" NAME="which_data_type" VALUE="linear-linear">
<TABLE border=1>
<TR><TD>Title (data lablel)</TD><TD>Xdata</TD><TD>Ydata 1</TD>
<TD>Ydata 2</TD>
<TD>Ydata 3</TD></TR>
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
</TD><TD>
<input TYPE="text" NAME="data_row4[<?php echo $i?>]"
	VALUE="<?php echo $data[$i][4]?>" SIZE="3">
</TD><TD>
</TR>
<?php 
	}
?>
</TABLE>
<p>
<SELECT NAME="which_plot_type">
<OPTION VALUE="area">Area
<OPTION VALUE="lines">Lines
<OPTION VALUE="linepoints">Line and Points
<OPTION VALUE="points">Points
</SELECT>
