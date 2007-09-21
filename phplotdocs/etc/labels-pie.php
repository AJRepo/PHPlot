<?php
# Testing phplot - Pie/text-data-single
require_once 'phplot.php';

$data = array(
  array('', 10),
  array('', 40),
  array('', 50),
);

$p =& new PHPlot(400,300);
# If running under CLI, just output the image data - no headers.
if (php_sapi_name() == 'cli') $p->SetIsInline(1);
$p->SetDataType('text-data-single');
$p->SetDataValues($data);
$p->SetPlotType('pie');
$p->DrawGraph();
