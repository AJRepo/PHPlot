<?php
# $Id$
# PHPlot test: Plot range test, implied independent variable (bar plot)
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'nx' => 1,                # Number of independent variable data points
  'ny' => 1,                # Number of data sets (dependent variables)
  'horizontal' => TRUE,    # True for horizontal bars, false for vertical
  );
require 'range-bars.php';
