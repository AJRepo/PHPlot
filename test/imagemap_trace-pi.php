<?php
# $Id$
# PHPlot test: Pie chart, with image map areas marked
# This produces a plot image with the areas that would be in an image map
# outlined. It does not produce HTML or an image map.
# For pie chart - this only shows the coordinates - it doesn't interpolate.
require_once 'phplot.php';

# Data for pie chart
$data = array(
  array('',  1),
  array('',  2),
  array('',  3),
  array('',  2),
  array('',  5),
);
$plot_type = 'pie';
$data_type = 'text-data-single';
$title = "PHPlot Test: pie chart with image map areas shown";

# Callback for 'data_points': Show the area
function store_map($im, $passthru, $shape, $segment, $unused,
   $xc, $yc, $wd, $ht, $start_angle, $end_angle)
{
    static $color = NULL;
    // Use white, not red, for this plot because a sector is red.
    if (!isset($color)) $color = imagecolorallocate($im, 255, 255, 255);

    $sa = deg2rad($start_angle);
    $ea = deg2rad($end_angle);
    $rx = $wd / 2;
    $ry = $ht / 2;
    $x1 = $xc + $rx * cos($sa);
    $y1 = $yc + $ry * sin($sa);
    $x2 = $xc + $rx * cos($ea);
    $y2 = $yc + $ry * sin($ea);

    imagesetthickness($im, 3);
    imageline($im, $xc, $yc, $x1, $y1, $color);
    imageline($im, $xc, $yc, $x2, $y2, $color);
    imageline($im, $x1, $y1, $x2, $y2, $color);

    imagesetthickness($im, 1);
}

$plot = new PHPlot(800, 600);
$plot->SetTitle($title);
$plot->SetImageBorderType('plain');
$plot->SetDataValues($data);
$plot->SetDataType($data_type);
$plot->SetPlotType($plot_type);
$plot->SetCallback('data_points', 'store_map');
$plot->DrawGraph();
