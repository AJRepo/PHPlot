<p>
Data Type: 
<a href="format_chart.php?which_data_type=text-data">text-data</a>//
<a href="format_chart.php?which_data_type=data-data">data-data</a>//
<a href="format_chart.php?which_data_type=function">function</a>//
<a href="format_chart.php?which_data_type=data-data-error">data-data-error</a>
</p>
<p>
<pre>
<input type="hidden" name="which_data_type" value="function" />
data-data as a function (just press submit)
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
    <option value="lines">Lines</option>
    <option value="linepoints">Lines and points</option>
    <option value="points">Points</option>
    <option value="thinbarline">Thin bars</option>
</select>
</p>
