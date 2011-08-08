<?php
# $Id$
# Testing phplot - Color Chart
require_once 'phplot.php';

$p = new PHPlot(800,700);

# This is cheating. The PHPlot constructor sets this->rgb_array to the
# default 'small' color maps. Key is the color name.
$colors = array_keys($p->rgb_array);
sort($colors);
$nc = count($colors);
# Make a data array with 1 point, $nc data sets:
$dp = array('');
for ($i = 0; $i < $nc; $i++) {
  $dp[] = $nc - $i;
}
$data = array($dp);

$p->SetPlotType('bars');
$p->SetDataType('text-data');
$p->SetDataValues($data);

# Use all colors:
$p->SetDataColors($colors);
$p->SetShading(0);

# Make a legend with all the color names:
$p->SetLegend($colors);

$p->SetPlotAreaWorld(0.1, 0, NULL, NULL);

$p->SetTitle('PHPlot Default Color Map');
$p->SetXTickLabelPos('none');
$p->SetXTickPos('none');
$p->SetYTickIncrement(1);

$p->DrawGraph();
