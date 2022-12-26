<?php
# $Id$
# PHPlot Example:  Flat Pie with options
require_once 'phplot.php';

$data = array(
  array('', 10),
  array('', 20),
  array('', 30),
  array('', 35),
  array('',  5),
);

$plot = new PHPlot(800,600);
$plot->SetImageBorderType('plain');
$plot->SetDataType('text-data-single');
$plot->SetDataValues($data);
$plot->SetPlotType('pie');

$colors = array('red', 'green', 'blue', 'yellow', 'cyan');
$plot->SetDataColors($colors);
$plot->SetLegend($colors);
$plot->SetShading(0);
$plot->SetLabelScalePosition(0.2);

$plot->DrawGraph();
