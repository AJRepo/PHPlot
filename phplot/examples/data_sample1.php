<p>
Data type: <a href="format_chart.php?which_data_type=text-data">text-data</a>//
<a href="format_chart.php?which_data_type=data-data">data-data</a>//
<a href="format_chart.php?which_data_type=function">function</a>//
<a href="format_chart.php?which_data_type=data-data-error">data-data-error</a><br />
</p>
<p>
Data type: (Text-data)<br />
</p>
<input type="hidden" name="which_data_type" value="text-data" />
<table border=1>
 <tr>
  <td>title (x axis)</td><td>ydata 1</td><td>ydata 2</td>
  <td>ydata 3</td> <td>ydata 4</td>
 </tr>
 
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
  <td>
   <input type="text" name="data_row0[<?php echo $i?>]" value="Year <?php echo $i?>">
  </td><td>
   <input type="text" name="data_row1[<?php echo $i?>]" value="<?php echo $a?>" size="2">
  </td><td>
   <input type="text" name="data_row2[<?php echo $i?>]" value="<?php echo $b?>" size="2">
  </td><td>
   <input type="text" name="data_row3[<?php echo $i?>]" value="<?php echo $c?>" size="2">
  </td><td>
   <input type="text" name="data_row4[<?php echo $i?>]" value="<?php echo $c+1?>" size="2">
  </td><td>
 </tr>
<?php 
	}
?>

</table>

<p>
Graph type:
<select name="which_plot_type">
  <option value="bars">Bars (text-data data only)</option>
  <option value="lines">Lines</option>
  <option value="pie">Pie (text-data data only)</option>
  <option value="linepoints">Line and points</option>
  <option value="points">Points</option>
  <option value="area">Area (text-data data only)</option>
</select>
</p>
