<?php 
///////////////////////////////////////////////
//This file is meant only to be called from the
//testing function quick_start.php. It will fail
//if called by itself.
//////////////////////////////////////////////
include("../phplot.php");
$graph = new PHPlot;
include("./data.php");
$graph->SetTitle("$which_title");
$graph->SetDataValues($example_data);
$graph->SetIsInline("1");
$graph->SetFileFormat("$file_format");
$graph->DrawGraph();
?>
