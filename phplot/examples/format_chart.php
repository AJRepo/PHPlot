<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Test Graph creation</title>
    <link type="text/css" rel="stylesheet" href="../doc/style.css" />
</head>
<body>

<h2>PHPlot test graph form</h2>
<p>Use this form to test many different options of PHPlot. You can test
every graph type supported for any of four different data types. You can
tweak as you like or you can leave everything as is and press "Submit" for
automatic values.
</p>
<form action="create_chart.php" method="post">
<center>
<table border="0">

        <tr><td colspan="2" class="hdr">Data Settings</td></tr>
        
<tr>
  <td colspan="2">
  <?php
      if ($_GET['which_data_type'] == "text-data")
          include("data_sample1.php"); 
      elseif ($_GET['which_data_type'] == "data-data")
          include("data_sample2.php");
      elseif ($_GET['which_data_type'] == "data-data-error")
          include("data_sample3.php");
      elseif ($_GET['which_data_type'] == "function") 
          include("data_sample4.php");
      else
          include("data_sample1.php");
  ?>
  </td>
</tr>

            <tr><td colspan="2" class="hdr">Sizes</td></tr>
        
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

            <tr><td colspan="2" class="hdr">Titles and labels</td></tr>
            
<tr>
  <td>Title:</td>
  <td><input type="text" name="title" value="this is a title" /></td>
</tr>
<tr>
  <td>Y axis title:</td>
  <td><input type="text" name="ylbl" value="revenue in millions" /></td>
</tr>
<tr>
  <td>Y axis title position:</td>
  <td>
    <select name="which_ytitle_pos">
      <option value="plotleft">Left of plot</option>
      <option value="plotright">Right of plot</option>
      <option value="both" selected>Both right and left</option>
      <option value="none">No Y axis title</option>
    </select>
  </td>
</tr>
<tr>
  <td>Y axis data labels position:</td>
  <td>
    <select name="which_ylabel_pos">
      <option value="plotleft">Left of plot</option>
      <option value="plotright">Right of plot</option>
      <option value="both" selected>Both right and left</option>
      <option value="none">No labels</option>
    </select>
  </td>
</tr>
<tr>
  <td>X axis title:</td>
  <td><input type="text" name="xlbl" value="years" /></td>
</tr>
<tr>
  <td>X axis title position:</td>
  <td>
    <select name="which_xtitle_pos">
      <option value="plotup">Up of plot</option>
      <option value="plotdown">Down of plot</option>
      <option value="both" selected>Both up and down</option>
      <option value="none">No X axis title</option>
    </select>
  </td>
</tr>
<tr>
  <td>X axis data labels position:</td>
  <td>
    <select name="which_xlabel_pos">
      <option value="plotup">Up of plot</option>
      <option value="plotdown">Down of plot</option>
      <option value="both" selected>Both up and down</option>
      <option value="none">No labels</option>
    </select>
  </td>
</tr>

            <tr><td colspan="2" class="hdr">Grid and ticks</td></tr>


<tr>
  <td>Grid drawn:</td>
  <td>
    <select name="which_draw_grid">
      <option value="x">Vertical grid</option>
      <option value="y">Horizontal grid</option>
      <option value="both" selected>Both grids</option>
      <option value="none">No grid</option>
    </select>
  </td>
</tr>
<tr>
  <td>Dashed grid?</td>
  <td>
    <select name="which_dashed_grid">
      <option value="1" selected>Yes</option>
      <option value="0">No</option>
    </select>
  </td>
</tr>
<tr>
  <td>X axis ticks position:</td>
  <td>
    <select name="which_xtick_pos">
      <option value="plotup">Up of plot</option>
      <option value="plotdown">Down of plot</option>
      <option value="both" selected>Both up and down</option>
      <option value="none">No ticks</option>
    </select>
  </td>
</tr>
<tr>
  <td>Y axis ticks position:</td>
  <td>
    <select name="which_ytick_pos">
      <option value="plotleft">Left of plot</option>
      <option value="plotright">Right of plot</option>
      <option value="both" selected>Both right and left</option>
      <option value="yaxis">Crossing Y axis (fixme?)</option>
      <option value="none">No ticks</option>
    </select>
  </td>
</tr>
<tr>
  <td>X tick increment:</td>
  <td><input type="text" name="which_xti" value="1" /></td>
</tr>
<tr>
  <td>Y tick increment:</td>
  <td><input type="text" name="which_yti" value="" /></td>
</tr>


            <tr><td colspan="2" class="hdr">Other</td></tr>


<tr>
  <td>Use TrueType font:</td>
  <td>
    <select name="which_use_ttf">
      <option value="0" selected>No</option>
      <option value="1">Yes</option>
    </select>
  </td>
</tr>
<tr>
  <td>X axis position:</td>
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
    <select name="which_fileformat">
      <option value="png">png</option>
      <option value="jpg">jpeg</option>
      <option value="gif">gif</option>
      <option value="wbmp">bmp</option>
    </select>
  </td>
</tr>
        <tr><td colspan="2" class="hdr"><input type="submit" /></td></tr>

</table>
</center>
</form>

<p>
Please visit <a href="http://phplot.sourceforge.net">PHPlot's site</a>, the
<a href="http://sourceforge.net/projects/phplot/">sourceforge project page</a>,
or see <a href="http://www.jeo.net/php/">more php code and examples</a> 
by Afan Ottenheimer of jeonet.
</p>

<p class="foot">$Id$</p>
</body>
</html>
