<p>
Data Type: 
<a href="format_chart.php?which_data_type=text-linear">text-linear</a>//
<a href="format_chart.php?which_data_type=linear-linear">linear-linear</a>//
<a href="format_chart.php?which_data_type=function">function</a>//
<a href="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</a>
</p>
<p>
<pre>
<input type="hidden" name="which_data_type" value="function" />
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
</p>
<p>
Chart type: 
<select name="which_plot_type">
    <option value="lines">lines</option>
    <option value="linepoints">line and points</option>
    <option value="points">points</option>
</select>
</p>
