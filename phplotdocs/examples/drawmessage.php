<?php
# PHPlot Example: Use DrawMessage() to display a message
require_once 'phplot.php';

$plot = new PHPlot(600, 400);
# Note: This font name is system dependent:
$plot->SetFontTTF('generic', 'LiberationSans-Italic.ttf', 14);
$plot->SetBackgroundColor('#ffcc99');
$plot->SetImageBorderWidth(8);
$plot->SetImageBorderColor('blue');
$plot->SetImageBorderType('raised');
#
# Here you would start to produce the plot, then detect something wrong ...
#
$message = "I'm sorry, Dave. I'm afraid I can't do that.\n"
         . "\n"
         . "You haven't supplied enough data to produce a plot. "
         . "Please try again at another time.";
$plot->DrawMessage($message, array(
    'draw_background' => TRUE,
    'draw_border' => TRUE,
    'reset_font' => FALSE,
    'wrap_width' => 50,
    'text_color' => 'navy'));
