<?php    


//Sample functions

//Linear-linear as a function
if ($which_data_type =="function") { 
	//Put function here
	$dx = ".3";
	$max = 6.4;
	$maxi = $max/$dx;
	for ($i=0; $i<$maxi; $i++) {
		$a = 4;
		$x = $dx*$i;
		$data[$i] = array("", $x, $a*sin($x),$a*cos($x),$a*cos($x+1)); 	
	}
	$which_data_type = "linear-linear";
} else { 
	while (list($key, $val) = each($data_row0)) {
		$data[$key] =array($data_row0[$key],$data_row1[$key],$data_row2[$key],$data_row3[$key],$data_row4[$key]); 	
	}

}

////////////////////////////////////////////////

	include("phplot.php");
	$graph = new PHPlot;

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
	if ($which_data_type = "text_linear") { 
		$graph->SetPlotAreaWorld(0,$miny_in,count($data),$maxy_in);
	}
}

	//$graph->SetPlotAreaWorld(0,-5,count($data),30);
	//$graph->SetPlotAreaWorld(0,-10,5,35);
	//$graph->SetPlotAreaPixels(150,50,600,400);

/*
//Even more settings
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
