<?
//Include the code
include("../phplot.php");

//Define the object
$graph = new PHPlot;

//Set some data
include("./data.php");
$graph->SetDataValues($example_data);

//$color = SetColor("blue");
//$graph->DrawDashedLine(1,1,50,50,4,1,0);


//Draw it
$graph->DrawGraph();

?>
