<?
include("../phplot.php");
$graph = new PHPlot;
include("./data.php");
$graph->SetDataType("linear-linear");
$graph->SetDrawXDataLabels(0);
//$graph->SetXGridLabelType("none");
$graph->SetDataValues($example_data);
$graph->DrawGraph();
?>
