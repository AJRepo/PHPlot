<?php
# $Id$
# PHPlot test: Horizontal Linepoints with missing data - 5
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => " (linepoints, DrawBrokenLines)",   # Title part 2
  'DBLines' => True,        # DrawBrokenLines: True or False or NULL to omit
  'PType' => 'linepoints',  # Plot Type: lines, linepoints, squared
  'horizontal' => True, # Horizontal plot
  );
require 'missing.php';
