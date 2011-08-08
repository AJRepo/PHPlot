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
  'plottype' => 'points',   # Plot type: lines, points, or linepoints.
        ), $tp);
require_once 'phplot.php';

# To get a repeatable test with 'random' data:
mt_srand(1);

# Make a data array with $tp['groups'] data sets.
$data = array(
  array('Jan', 40, 2, 4), array('Feb', 30, 3, 4), array('Mar', 20, 4, 4),
  array('Apr', 10, 5, 4), array('May',  3, 6, 4), array('Jun',  7, 7, 4),
  array('Jul', 10, 8, 4), array('Aug', 15, 9, 4), array('Sep', 20, 5, 4),
  array('Oct', 18, 4, 4), array('Nov', 16, 7, 4), array('Dec', 14, 3, 4),
);
$data = array();
$ng = $tp['groups'];
for ($pt = 0; $pt < 12; $pt++) {
  # It's a test script. I can be obscure if I want to.
  $row = array(strftime('%b', mktime(12, 12, 12, $pt+1, 1, 2000)));
  for ($r = 0; $r < $ng; $r++) {
    $row[] = mt_rand(0, 20);
  }
  $data[] = $row;
}

$plot = new PHPlot();
$plot->SetTitle($tp['title'] . $tp['suffix']);
$plot->SetDataType('text-data');
$plot->SetDataValues($data);
$plot->SetPlotType($tp['plottype']);
$plot->SetPlotAreaWorld(NULL, 0, NULL, 20);

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
