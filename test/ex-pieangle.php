<?php
# $Id$
# PHPlot Example: Pie chart with varying start angle and direction
# Note: This requires PHPlot-6.0.0 or higher.
require_once 'phplot.php';
require_once 'config.php'; // For font information

# Check for PHPlot method and skip the test if it is missing:
if (!method_exists('PHPlot', 'SetPieStartAngle')) {
    echo "Skipping test because it requires SetPieStartAngle()\n";
    exit(2);
}

$pie_slices = 6;
$base_angle = 0;

# This callback is used to label each plot with a title.
# The x_title font is set below and used here.
function draw_plot_title($img, $args)
{
    list($plot, $x, $y, $title) = $args;
    $text_color = imagecolorresolve($img, 0, 0, 0);
    $plot->DrawText('x_title', 0, $x, $y, $text_color, $title, 'center', 'top');
}

# Produce one plot tile
function draw_plot($plot, $start_angle, $direction, $xbase, $ybase)
{
    $plot->SetPieStartAngle($start_angle);
    $plot->SetPieDirection($direction);
    $plot->SetPlotAreaPixels($xbase, $ybase, $xbase + 200, $ybase + 200);
    $title = "Start @{$start_angle}d $direction";
    $plot->SetCallback('draw_all', 'draw_plot_title',
                    array($plot, $xbase + 100, $ybase + 200, $title));
    $plot->DrawGraph();
}

# Make a data array with equal-size slices:
$data = array_fill(0, $pie_slices, array('', 1));

$plot = new PHPlot(800, 600);
$plot->SetDataValues($data);
$plot->SetDataType('text-data-single');
$plot->SetPlotType('pie');
$plot->SetShading(0);
$plot->SetImageBorderType('plain');
$plot->SetPrintImage(False);
$plot->SetTitle("Pie Chart - Vary Start Angle and Direction\n"
              . "(CW = Clockwise, CCW = Counter-clockwise)");

# Setting the path for TTF should not be necessary, but
# it is on systems with thread-safe PHP (probably a bug).
$plot->SetTTFPath($phplot_test_ttfdir);

# Configure pie labels: Show sector index, inside the pie, in a large font.
$plot->SetPieLabelType('index');
$plot->SetLabelScalePosition(0.25);
#   Use the default TrueType font at 36 points.
$plot->SetFontTTF('generic', '', 36);

# This font is used by the callback to label each plot:
#   Use the default TrueType font at 16 points.
$plot->SetFontTTF('x_title', '', 16); // Use the default TTF font at 16 pts

# Draw the plot tiles:
draw_plot($plot, $base_angle +   0, 'CCW',   0,  50);
draw_plot($plot, $base_angle +  90, 'CCW', 200,  50);
draw_plot($plot, $base_angle + 180, 'CCW', 400,  50);
draw_plot($plot, $base_angle + 270, 'CCW', 600,  50);

draw_plot($plot, $base_angle +   0, 'CW',    0, 300);
draw_plot($plot, $base_angle +  90, 'CW',  200, 300);
draw_plot($plot, $base_angle + 180, 'CW',  400, 300);
draw_plot($plot, $base_angle + 270, 'CW',  600, 300);

# Done:
$plot->PrintImage();
