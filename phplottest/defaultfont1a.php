<?php
# $Id$
# Testing phplot - Default TT font (1a): No default path or font set, usettf
require_once 'phplot.php';

$data = array(
  array('A', 3,  6),
  array('B', 2,  4),
  array('C', 1,  2),
);
$p = new PHPlot(800, 800);
$p->SetDataType('text-data');
$p->SetDataValues($data);
$p->SetPlotType('bars');
$p->SetTitle('TTF default not set, SetUseTTF(true)');
$p->SetXTitle('X Axis Title');
$p->SetYTitle('Y Axis Title');
$p->SetUseTTF(True);
$p->DrawGraph();
fwrite(STDERR, "OK defaultfont1a: default_ttfont=".$p->default_ttfont."\n");
