<?php
# $Id$
# PHPlot test: Bars with datalabel - 2
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => " 4x3",       # Title part 2
  'ngroups' => 4,           # Number of bar groups
  'nbars' => 3,             # Number of bars per group
  'fontsize' => NULL,       # Label font size or NULL to omit
  'shading' => NULL,        # SetShading: 0 or pixels or NULL to omit
  );
require 'bars-datalabel.php';
