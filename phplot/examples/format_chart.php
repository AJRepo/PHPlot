<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- $Id$ -->
<html>
<head>
    <title>Test Graph creation</title>
    <style type="text/css">
    <!--
    .hdr { text-align:center;font-weight:bold; }
    -->
    </style>
</head>
<body>

<h2>PHPlot test graph form</h2>
<p>
NOTE! For this example script, if you change a parameter you have to reload
the graph generated.
</p>
<form action="create_chart.php" method="post">
<center>
<table border="0">
<tr><td colspan="2" class="hdr">Data Settings</td></tr>
<tr>
  <td colspan="2">
  <?php
      if ($_GET['which_data_type'] == "text-linear")
          include("data_sample1.php"); 
      elseif ($_GET['which_data_type'] == "linear-linear")
          include("data_sample2.php");
      elseif ($_GET['which_data_type'] == "linear-linear-error")
          include("data_sample3.php");
      elseif ($_GET['which_data_type'] == "function") 
          include("data_sample4.php");
      else
          include("data_sample1.php");
  ?>
  </td>
</tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td colspan="2" class="hdr">Optional settings (leave blank for automatic values)</td></tr>
<tr>
  <td>Width of graph in pixels:</td>
  <td><input type="text" name="xsize_in" value="600" /></td>
</tr>
<tr>
  <td> Height of graph in pixels:</td>
  <td><input type="text" name="ysize_in" value="400" /></td>
</tr>
<tr>  
  <td>Maximum height of graph in y axis units:</td>
  <td><input type="text" name="maxy_in" value="" /></td>
</tr>
<tr>
  <td>Minimum height of graph in y axis units:</td>
  <td><input type="text" name="miny_in" value="" /></td>
</tr>
<tr>
  <td>Y axis label:</td>
  <td><input type="text" name="ylbl" value="revenue in millions" /></td>
</tr>
<tr>
  <td>X axis label:</td>
  <td><input type="text" name="xlbl" value="years" /></td>
</tr>
<tr>
  <td>Title:</td>
  <td><input type="text" name="title" value="this is a title" /></td>
</tr>
<tr>
  <td>Vertical tick increment:</td>
  <td><input type="text" name="which_vti" value="" /></td>
</tr>
<tr>
  <td>Horizontal tick increment:</td>
  <td><input type="text" name="which_hti" value="1" /></td>
</tr>
<tr>
  <td>x axis position:</td>
  <td><input type="text" name="which_xap" value="1" /></td>
</tr>
<tr>
  <td>Point Type:</td>
  <td>
    <select name="which_dot">
	  <option>diamond</option>
  	  <option>rect</option>
	  <option>circle</option>
	  <option>triangle</option>
      <option>dot</option>
	  <option>line</option>
	  <option>halfline</option>
    </select>
  </td>
</tr>
<tr>
  <td>File format:</td>
  <td>
    <select name="which_file_format">
      <option>png</option>
      <option>jpeg</option>
      <option>gif</option>
    </select>
  </td>
</tr>
<tr>
  <td></td>
  <td><input type="submit" /></td>
</tr>
</table>
</center>
</form>
<p>
Please visit <a href="http://phplot.sourceforge.net">PHPlot's sourceforge site</a>.
Or see <a href="http://www.jeo.net/php/">more php code and examples</a> 
by Afan Ottenheimer of jeonet.
</p>

</body>
</html>
