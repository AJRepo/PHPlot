<?
include("./data_date2.php");
include("../phplot.php");
$graph = new PHPlot;
$graph->SetDataType("data-data-error");  //Must be called before SetDataValues

$graph->SetImageArea(600,400);
$graph->SetXGridLabelType("time");
$graph->SetXDataLabelAngle(90);
$graph->SetXLabel("");
$graph->SetYLabel("Volume");
$graph->SetVertTickIncrement(20);
$graph->SetHorizTickIncrement(2679000);
$graph->SetXTimeFormat("%b %y");
$graph->SetDataValues($data_level);
$graph->SetPlotType("lines");
$graph->SetErrorBarShape("line");
$graph->SetPointShape("halfline");
$graph->SetYScaleType("log");
$graph->SetLineWidth(1);
//$graph->SetDrawXDataLabels(1);
$graph->SetPlotAreaWorld(883634400,1,915095000,140);
$graph->DrawGraph();
?>
