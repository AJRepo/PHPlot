<?php
# $Id$
# Unit test: PHPlot DrawText function - GD, 90deg, 1 line
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'textangle' => 90,   # Text angle. GD fonts only allow 0 and 90.
  'nlines' => 1,      # Number of lines of text
  );
require 'drawtext.php';
