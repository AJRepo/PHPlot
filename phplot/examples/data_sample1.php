Data Type: <A HREF="format_chart.php?which_data_type=text-linear">text-linear</A>//
<A HREF="format_chart.php?which_data_type=linear-linear">linear-linear</A>//
<A HREF="format_chart.php?which_data_type=function">function</A>//
<A HREF="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</A><br>
<p>
Data: (text-linear)<br>
<INPUT TYPE="hidden" NAME="which_data_type" VALUE="text-linear">
<TABLE border=1>
<TR><TD>Title (x axis)</TD><TD>Ydata 1</TD><TD>Ydata 2</TD>
<TD>Ydata 3</TD>
<TD>Ydata 4</TD></TR>
<?php 
	srand ((double) microtime() * 12341234);
	$a = 25;
	$b = 10;
	$c = -5;
	for ($i=0; $i<5; $i++) {
		$a += rand(-2, 2);
		$b += rand(-5, 5);
		$c += rand(-2, 2);

?>
<tr>
<TD>
<input TYPE="text" NAME="data_row0[<?php echo $i?>]" VALUE="year <?php echo $i?>">
</TD><TD>
<input TYPE="text" NAME="data_row1[<?php echo $i?>]" VALUE="<?php echo $a?>" SIZE="2">
</TD><TD>
<input TYPE="text" NAME="data_row2[<?php echo $i?>]" VALUE="<?php echo $b?>" SIZE="2">
</TD><TD>
<input TYPE="text" NAME="data_row3[<?php echo $i?>]" VALUE="<?php echo $c?>" SIZE="2">
</TD><TD>
<input TYPE="text" NAME="data_row4[<?php echo $i?>]" VALUE="<?php echo $c+1?>" SIZE="2">
</TD><TD>
</TR>
<?php 
	}
?>
</TABLE>
<p>
<SELECT NAME="which_plot_type">
<OPTION VALUE="bars">Bars (text-linear data only)
<OPTION VALUE="lines">Lines
<OPTION VALUE="pie">Pie (text-linear data only)
<OPTION VALUE="linepoints">Line and Points
<OPTION VALUE="points">Points
<OPTION VALUE="area">Area (text-linear data only)
</SELECT>
