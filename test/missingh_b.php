<?php
# $Id$
# PHPlot test: Horizontal Plots with missing data - plot type bars
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => " (bars)",   # Title part 2
  'PType' => 'bars',
  'xmiss1' => 2,
  'xmiss2' => 8,
  'horizontal' => True, # Horizontal plot
  );
require 'missing.php';
