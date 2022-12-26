<?php
# $Id$
# PHPlot test - transparency - truecolor, set transparent then set background
require_once 'phplot.php';
$data = array(array('A', 6), array('B', 4), array('C', 2), array('D', 0));
$p = new PHPlot_truecolor;
$p->SetTitle('Truecolor, Set transparent, Set background color');
$p->SetDataValues($data);
$p->SetPlotType('bars');
$p->SetTitleColor('green'); // For contrast vs black/clear background
$p->SetTransparentColor('yellow');
$p->SetBackgroundColor('yellow');
$p->DrawGraph();
