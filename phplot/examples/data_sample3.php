<p>
 Data type: 
 <a href="format_chart.php?which_data_type=text-linear">text-linear</a>//
 <a href="format_chart.php?which_data_type=linear-linear">linear-linear</a>//
 <a href="format_chart.php?which_data_type=function">function</a>//
 <a href="format_chart.php?which_data_type=linear-linear-error">linear-linear-error</a>
</p>
<p>
Data set as X, Y, Y+, Y- <br />
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
Data: (Linear-Linear)
</p>
<input type="hidden" name="which_data_type" value="linear-linear-error" />
<table border=1>
 <tr><td>Title (data label)</td><td>X data</td><td>Y data 1</td><td>Error +</td><td>Error -</td></tr>
 <?php
    for ($i=0; $i<5; $i++) {
 ?>
 <tr>
  <td>
   <input type="text" name="data_row0[<?php echo $i; ?>]" value="<?php echo $data[$i][0]; ?>" />
  </td><td>
   <input type="text" name="data_row1[<?php echo $i; ?>]" value="<?php echo $data[$i][1]; ?>" size="3"/>
  </td><td>
   <input type="text" name="data_row2[<?php echo $i; ?>]" value="<?php echo $data[$i][2]; ?>" size="3" />
  </td><td>
   <input type="text" name="data_row3[<?php echo $i; ?>]" value="<?php echo $data[$i][3]; ?>" size="3" />
  </td><td>
   <input type="text" name="data_row4[<?php echo $i; ?>]" value="<?php echo $data[$i][4]; ?>" size="3" />
  </td>
 </tr>
 <?php
    }
 ?>
</table>

<p>
Graph type: 
<select name="which_plot_type">
 <option value="lines">lines</option>
 <option value="linepoints">line and points</option>
 <option value="points">points</option>
</select>
&nbsp; &nbsp;&nbsp;
Error bar type:
<select name="which_error_type"> 
 <option value="tee">tee</option>
 <option value="line">line</option>
</select>
</p>
