<?
include("../phplot.php");
$graph = new PHPlot;
include("./data.php");
$graph->SetDataValues($example_data);
$graph->DrawGraph();
?>
