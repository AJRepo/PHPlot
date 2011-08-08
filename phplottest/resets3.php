<?php
# $Id$
# Testing PHPlot: Set/Reset parameters - case 2
# This produces 2 plots: one with parameters set, and
# the second after reset.
# (resets2 tests tick increment; resets3 tests num ticks. These
# interact so they can't be done in one test.)
require_once 'phplot.php';

$data = array();
# This is a cubic equation with roots at -8, 2, 10
for ($x = -10; $x <= 10; $x++)
    $data[] = array('', $x, ($x + 8) * ($x - 2) * ($x - 10));

$p = new PHPlot(400, 800);
$p->SetPrintImage(FALSE);
$p->SetPlotBorderType('full');

$p->SetTitle("Set/Reset Parameters Test (2)\n"
   . "Top: Parameters Set\n"
   . "Bottom: Parameters Reset");
$p->SetDataType('data-data');
$p->SetDataValues($data);
$p->SetPlotType('lines');
$p->SetLegend('Y = F(X)');
$p->SetLegendPixels(100, 200);
$p->SetNumXTicks(5);
$p->SetNumYTicks(8);
$p->SetXTitle('X Axis with 5 ticks');
$p->SetYTitle('Y Axis with 8 ticks');
$p->SetXAxisPosition(-228);
$p->SetYAxisPosition(7);
$p->SetPlotAreaPixels(70, 80, 380, 400);
$p->DrawGraph();

$p->SetLegendPixels();
$p->SetNumXTicks();
$p->SetNumYTicks();
$p->SetXTitle('X Axis');
$p->SetYTitle('Y Axis');
$p->SetXAxisPosition();
$p->SetYAxisPosition();
$p->SetPlotAreaPixels(70, 450, 380, 750);
$p->DrawGraph();

$p->PrintImage();
