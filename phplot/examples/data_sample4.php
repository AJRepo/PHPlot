Data Type: <A HREF="format_chart.php?which_data_type=text-linear">text-linear</A>//
<A HREF="format_chart.php?which_data_type=linear-linear">linear-linear</A>//
<A HREF="format_chart.php?which_data_type=function">function</A>//
<A HREF="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</A><br>
<p>
<pre>
<INPUT TYPE="hidden" NAME="which_data_type" VALUE="function">
Linear-linear as a function (just press submit)
	$dx = ".3";
	$max = 6.4;
	$maxi = $max/$dx;
	for ($i=0; $i<$maxi; $i++) {
		$a = 4;
		$x = $dx*$i;
		$data[$i] = array("", $x, $a*sin($x),$a*cos($x),$a*cos($x+1)); 	
	}
</pre>
Chart Type: <SELECT NAME="which_plot_type">
<OPTION VALUE="lines">Lines
<OPTION VALUE="linepoints">Line and Points
<OPTION VALUE="points">Points
</SELECT>
<p>
