<?
include("../phplot.php");
include("../phplot_data.php");
$graph = new PHPlot_Data;
include("./data.php");
$graph->SetDataType("linear-linear");
$graph->SetDataValues($example_data);
//$graph->DoMovingAverage(4,1,1);
$graph->DoScaleData(1,1);
$graph->SetLegendPixels(100,100,"");
$graph->DrawGraph();
?>
