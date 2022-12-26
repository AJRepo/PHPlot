<?php
# $Id$
# PHPlot test - Pie Chart Sizing and Label Variations - Size/Label Position (g)
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => 'Sizing/label position - force margins',          # Title part 2
  'data_choice' => 2,          # Select data array: 1 or 2
  'n_slices' => 44,            # For data_choice==2, number of slices
  'pie_diam_factor' => NULL,   # If set, oblateness for shaded pie (dflt=0.5)
  'shading' => NULL,           # If set, pie shading SetShading()
  'label_pos' => NULL,         # If set, label position SetLabelScalePosition()
  'pie_label_args' => array(), # If set array of args to SetPieLabelType()
  'ttfonts' => True,          # Use TrueType fonts?
  'font_size' => 8,         # If set, TrueType or GD font size
  'plot_border' => 'full',     # If set, plot border type SetPlotBorderType()
  'plot_margins' => array(35,35,NULL,200),      # If set, array SetMarginsPixels(l,r,t,b)
  'image_aspect' => 'S',       # Image aspect: S=square, P=portrait, L=landscape
  );
require 'piesize.php';
