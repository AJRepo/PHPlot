<?php
# $Id$
# Legend shape marker tests - case 2
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => 'Non-points plot, revert to colorboxes, TTF',  # Title line 2
  'useshapes' => True,     # True for shape markers, false for color boxes
  'fontsize' => 14,       # Use TT font at this size
  'plottype' => 'lines',   # Plot type, points or linepoints
  );
require 'legshape.php';
