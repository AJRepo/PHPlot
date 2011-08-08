<?php
# $Id$
# Unit test: PHPlot DrawText function - TrueType at 180 degrees, multiple lines
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'use_ttf' => True,        # True to use TTF text, False for GD text
  'textangle' => 180,   # Text angle. GD fonts only allow 0 and 90.
  'nlines' => 5,      # Number of lines of text
  );
require 'drawtext.php';
