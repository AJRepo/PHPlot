<?php
/* $Id$ */

///////////////////////////////////////////////
//This file is meant only to be called from the
//test page quick_start.php (or test_setup.php)
//It will fail if called by itself.
//////////////////////////////////////////////

// From PHP 4.?, register_globals is off, take it into account (MBD)

include('../phplot.php');
$graph = new PHPlot;
include('./data.php');
$graph->SetTitle("$_GET[which_title]");
$graph->SetDataValues($example_data);
$graph->SetIsInline('1');
$graph->SetFileFormat("$_GET[which_format]");
$graph->DrawGraph();
?>
