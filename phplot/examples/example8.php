<?
include("./data_date2.php");
include("../phplot.php");
$graph = new PHPlot;
$graph->SetPrintImage(0); //Don't draw the image yet

$graph->SetDataType("data-data-error");  //Must be called before SetDataValues

$graph->SetNewPlotAreaPixels(90,40,540,190);
$graph->SetDataValues($example_data);

$graph->SetXGridLabelType("time");
$graph->SetImageArea(600,400);
$graph->SetXDataLabelAngle(90);
$graph->SetXLabel("");
$graph->SetYLabel("Price");
$graph->SetVertTickIncrement(20);
$graph->SetHorizTickIncrement(2679000);
$graph->SetXTimeFormat("%b %y");
$graph->SetPlotType("lines");
$graph->SetErrorBarShape("line");
$graph->SetPointShape("halfline");
$graph->SetYScaleType("log");
$graph->SetLineWidth(1);
//$graph->SetDrawXDataLabels(1);
$graph->SetPlotAreaWorld(883634400,1,915095000,140);

$graph->DrawGraph();
?>
<?
unset($example_data);
$graph->SetPrintImage(1); //Now draw the image 

$graph->SetYScaleType("linear");
include("./data_date.php");

$graph->SetDataType("data-data");  //Must be called before SetDataValues

$graph->SetDataValues($example_data);
$graph->SetNewPlotAreaPixels(90,260,540,350);
$graph->SetDataValues($example_data);

$graph->SetXGridLabelType("time");
$graph->SetXDataLabelAngle(90);
$graph->SetXLabel("");
$graph->SetYLabel("Volume");
$graph->SetVertTickIncrement(30);
$graph->SetXTimeFormat("%b %y");
$graph->SetHorizTickIncrement(2679000);
$graph->SetPlotType("thinbarline");
//$graph->SetDrawXDataLabels(1);
$graph->SetPlotAreaWorld(883634400,0,915095000,90);
$graph->DrawGraph();
$graph->PrintImage();
?>
