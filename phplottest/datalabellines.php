<?php
# $Id$
# Testing PHPlot: Data Label Lines
# This is a parameterized test. Other scripts can set $tp and then include
# this script. The parameters are shown in the defaults array below:
if (!isset($tp)) $tp = array();
$tp = array_merge(array(
  'title' => 'Data Label Lines',
  'suffix' => " (no labels, no lines)",           # Title part 2
  'groups' => 1,            # Number of data groups (1 or more)
  'labelpos' => 'none',     # X Data Label Position: plotup, plotdown, both, none
  'labellines' => False,    # Draw data lines? False or True
  'plottype' => 'points',   # Plot type
        ), $tp);
require_once 'phplot.php';

$n_x = 12; // Fixed number of  X values (rows)
$max_y = 20; // Max random Y


# To get a repeatable test with 'random' data:
mt_srand(1);

# Make a data array with $tp['groups'] data sets.
$data = array();
$ng = $tp['groups'];
if ($tp['plottype'] != 'bubbles') {
    // For lines, points, linepoints:
    $data_type = 'text-data';

    for ($pt = 0; $pt < $n_x; $pt++) {
        $row = array(strftime('%b', mktime(12, 12, 12, $pt+1, 1, 2000)));
        for ($r = 0; $r < $ng; $r++) {
            $row[] = mt_rand(0, $max_y);
        }
        $data[] = $row;
    }

} else {
    // Special case data set for bubbles plot, needs 3D data.
    $data_type = 'data-data-xyz';

    // Note: X values matche auto-generated values for text-data: 0.5, 1.5, etc
    for ($pt = 0; $pt < $n_x; $pt++) {
        $row = array(strftime('%b', mktime(12, 12, 12, $pt+1, 1, 2000)),
                     $pt + 0.5);
        for ($r = 0; $r < $ng; $r++) {
            $row[] = mt_rand(0, $max_y); // Y value
            $row[] = mt_rand(0, 100); // Z value
        }
        $data[] = $row;
    }
}

$plot = new PHPlot();
$plot->SetTitle($tp['title'] . $tp['suffix']);
$plot->SetDataType($data_type);
$plot->SetDataValues($data);
$plot->SetPlotType($tp['plottype']);
$plot->SetPlotAreaWorld(0, 0, $n_x, $max_y);
# Position data labels:
$plot->SetXDataLabelPos($tp['labelpos']);
# Turn data label lines on or off:
$plot->SetDrawXDataLabelLines($tp['labellines']);

# Turn off Tick labels and X Tick marks - they aren't used.
$plot->SetXTickLabelPos('none');
$plot->SetXTickPos('none');
# Don't draw grids, so we can see the data lines.
$plot->SetDrawXGrid(False);
$plot->SetDrawYGrid(False);
$plot->DrawGraph();
