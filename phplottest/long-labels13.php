<?php
# $Id$
# X label size and angle test - TTF 3
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => ' TTF 12pt',  # Title part 2
  'MaxLen' => 25,           # Max label length (int * 5)
  'TTF' => True,            # If True, use TrueType fonts, else GD fonts
  'FontSize' => 12,         # GD font size/TTF font point size, NULL to omit
  );
require 'long-labels.php';
