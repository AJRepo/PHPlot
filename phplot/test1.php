<?php    

include("phplot.php");
$graph = new PHPlot;


//linear-linear-error data 
unset($data);
$a = 9.62;
srand ((double) microtime() * 1000000);
$label[0] = "October";
$label[5] = "Day 5";
$label[10] = "Day 10";
$label[15] = "Day 15";
$label[20] = "Day 20";
$label[25] = "Day 25";
for ($i=0; $i<=30; $i++){ 
	$a += rand(-1, 2);
	$b = rand(0,1);
	$c = rand(0,1);
	$data[] = array("$label[$i]",$i+1,$a,$b,$c);
}
	$graph->SetDataType("linear-linear-error");  //Must be first thing
	$graph->SetPrecision(0);

////////////////////////////////////////////////

	$graph->SetDataType($which_data_type);  //Must be first thing
	$graph->SetUseTTF("0");
	$graph->SetDrawYGrid("1"); // 1 = true
	$graph->SetFileFormat("gif");
	$graph->SetDataValues($data);
    $graph->SetImageArea($XSIZE_in, $YSIZE_in);
	$graph->SetVertTickIncrement($which_vti);
	$graph->SetHorizTickIncrement($which_hti);
    $graph->SetLineWidth("1");
	$graph->SetPointShape("$which_dot");
	$graph->SetErrorBarShape("$which_error_type");
	$graph->SetPlotType($which_plot_type);
	$graph->SetXLabel($xlbl);
	$graph->SetYLabel($ylbl);
	$graph->SetTitle($title);
	$graph->SetDataColors(
		array("blue","green","yellow","red"),  //Data Colors
		array("black")							//Border Colors
	);  
if ($maxy_in) { 
	$graph->SetPlotAreaWorld(0,$miny_in,count($data),$maxy_in);
}

	//$graph->SetPlotAreaWorld(0,-5,count($data),30);
	//$graph->SetPlotAreaWorld(0,-10,5,35);
	//$graph->SetPlotAreaPixels(150,50,600,400);

/*
//Other settings
		$graph->SetPlotBgColor(array(222,222,222));
		$graph->SetBackgroundColor(array(200,222,222)); //can use rgb values or "name" values
		$graph->SetTextColor("black");
		$graph->SetGridColor("black");
		$graph->SetLightGridColor(array(175,175,175));
		$graph->SetTickColor("black");
		$graph->SetTitleColor(array(0,0,0)); // Can be array or name

	$graph->SetMaxStringSize(9);
    $graph->SetChartBorderColor("black");
    $graph->SetChartTitleSize(14);
    $graph->SetChartTitleColor("blue");
	$graph->SetLegend(array("a", "f", "r", "b"));
    $graph->SetLegendPosition(2);
	$graph->SetAxisFontSize(8);
    $graph->SetAxisColor("black");
    $graph->SetAxisTitleSize(12);
    $graph->SetTickLength(2);
    $graph->SetGridX(6);
    $graph->SetGridY(0);
    $graph->SetGridColor("white");
	$graph->SetLineThickness(1);
	$graph->SetPointSize(2); //Size of dots
	$graph->SetPointShape("dots");
*/
    $graph->DrawGraph();
?>
