<?
include("../phplot.php");
$graph = new PHPlot;
include("./data.php");
$graph->SetDataType("linear-linear");
$graph->SetDataValues($example_data);
$graph->DrawGraph();
?>
