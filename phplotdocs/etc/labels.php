<?php
# Testing phplot - Bars.
require_once 'phplot.php';

$data = array(
  array('Jan', 40),
  array('Feb', 30),
  array('Mar', 20),
  array('Apr', 10),
  array('May',  3),
  array('Jun',  7),
);

$p =& new PHPlot(400,300);
# If running under CLI, just output the image data - no headers.
if (php_sapi_name() == 'cli') $p->SetIsInline(1);
$p->SetDataType('text-data');
$p->SetDataValues($data);

# Turn off X tick labels, use our data labels only:
$p->SetXTickLabelPos('none');
# Then we have to turn off the ticks too, because they aren't placed right:
$p->SetXTickPos('none');

$p->SetPlotType('bars');
$p->DrawGraph();
