<?php
include("./data_date2.php");
include("../phplot.php");
$graph = new PHPlot(600,400);
$graph->SetPrintImage(0); //Don't draw the image yet

$graph->SetDataType("data-data-error");  //Must be called before SetDataValues

$graph->SetNewPlotAreaPixels(90,40,540,190);
$graph->SetDataValues($example_data);

$graph->SetXGridLabelType("time");
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
$graph->SetPlotAreaWorld(883634400,1,915095000,140);

$graph->DrawGraph();
?>
<?php
//Now do the second chart on the image
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
$graph->SetPlotType("thinbarline");

//Set how to display the x-axis ticks
$graph->SetXTimeFormat("%b %y");
$graph->SetHorizTickIncrement(2679000);
$graph->SetXAxisPosition(0);  //Have to reset it after log plots

//Set Plot to go from x = Jan 1 1998, to x = Dec 31 1998
//	and from y = 0 to y = 90
$graph->SetPlotAreaWorld(883634400,0,915095000,90);

$graph->DrawGraph();

//Print the image
$graph->PrintImage();
?>
