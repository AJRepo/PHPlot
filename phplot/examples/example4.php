<?php    

include("../phplot.php");
$graph = new PHPlot;


//linear-linear-error data 
unset($data);
$a = 9.62;
srand ((double) microtime() * 1000);
////////////////////////////////////////////////
/////Note: for $label[5] to appear on the X axis,
/////      there must be a horizontal tick mark at x=5, etc.
/////////////////////////////////////////////
$label[0] = "October";
$label[5] = "Day 5";
$label[10] = "Day 10";
$label[15] = "Day 15";
$label[20] = "Day 20";
$label[25] = "Day 25";
$label[30] = "Day 30";
for ($i=0; $i<=30; $i++){ 
	$a += rand(-1, 2);
	$b = rand(0,1);
	$c = rand(0,1);
	$data[] = array("$label[$i]",$i+1,$a,$b,$c);
}
	$graph->SetDataType("linear-linear-error");  //Must be first thing
	$graph->SetPrecisionX(0);
	$graph->SetPrecisionY(0);
	$graph->SetUseTTF("0");
	$graph->SetDrawYGrid("1"); // 1 = true
	$graph->SetFileFormat("gif");
	$graph->SetDataValues($data);
    $graph->SetImageArea(600, 400);
	$graph->SetVertTickIncrement("");
	$graph->SetHorizTickIncrement(1);
    $graph->SetLineWidth("1");
	$graph->SetPointShape("halfline");
	$graph->SetErrorBarShape("line");
	$graph->SetPlotType("points");
	$graph->SetXGridLabelType("title");
	$graph->SetXLabel("Day");
	$graph->SetYLabel("index");
	$graph->SetTitle("Stock Market Data");
	$graph->SetDataColors(
		array("blue","green","yellow","red"),  //Data Colors
		array("black")							//Border Colors
	);  

	//$graph->SetPlotAreaWorld(0,0,32,30);
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

*/
    $graph->DrawGraph();
?>
