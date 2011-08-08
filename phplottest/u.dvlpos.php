<?php
# $Id$
# Unit test for CheckDataValueLabel label position and alignment
require_once 'phplot.php';
require 'usupport.php';

# Set to true for verbose test output:
// $test_debug = True;
$error = '';
$tests = 0;
$fails = 0;


# ====== Test support functions ======

# Extend the class to allow access to protected method.
class PHPlot_test extends PHPlot
{
    // CheckDataValueLabels()
    function test_CheckDataValueLabels($flag, &$x, &$y, &$h, &$v)
    {
        return $this->CheckDataValueLabels($flag, $x, $y, $h, $v);
    }
}

# ===== Test Wrappers ======
function test($msg, $angle, $dist, $expect_xoff, $expect_yoff, $expected_align)
{
  global $p, $test_debug, $tests, $fails, $error;

  $failed = False;
  $tests++;

  if (isset($angle)) $p->data_value_label_angle = $angle;
  else unset($p->data_value_label_angle);
  if (isset($dist)) $p->data_value_label_distance = $dist;
  else unset($p->data_value_label_distance);
  // 'plotin' arg always enables labels.
  $p->test_CheckDataValueLabels('plotin', $x_adj, $y_adj, $h_align, $v_align);
  $align = $h_align . $v_align;
  if ($test_debug) {
      echo "CheckDataValueLabels(angle=" . (isset($angle) ? $angle : "UNSET")
         . ', dist=' . (isset($dist) ? $dist : "UNSET") . ")\n  "
          . "offset=($x_adj, $y_adj) alignment=$align\n";
  }

  if (!expect_equal($expected_align, $align, "$msg alignment", $error))
      $failed = True;

  // Offsets need to be checked within a fuzz factor. Use 1 since the
  // function actually returns an integer number of pixels.
  if (!expect_float($expect_xoff, $x_adj, 1, "$msg x-offset", $error)
   || !expect_float($expect_yoff, $y_adj, 1, "$msg y-offset", $error))
      $failed = True;

  if ($failed) $fails++;
}

# ===== Testing =====

$p = new PHPlot_test();

test('Default angle and dist', NULL, NULL,  0,   -5, 'centerbottom');
test('Default angle @ 100   ', NULL,  100,  0, -100, 'centerbottom');
test('270d @ 100            ', 270,   100,  0,  100, 'centertop');
test('180d @ default dist   ', 180,  NULL, -5,    0, 'rightcenter');

//                ang   dist  Xexp  Yexp  Alignexp
test("Angle   0",   0,   10,   10,    0, 'leftcenter');
test("Angle  15",  15,   10,    9,   -2, 'leftcenter');
test("Angle  30",  30,   10,    8,   -5, 'leftbottom');
test("Angle  45",  45,   10,    7,   -7, 'leftbottom');
test("Angle  60",  60,   10,    5,   -8, 'leftbottom');
test("Angle  75",  75,   10,    2,   -9, 'centerbottom');

test("Angle  90",  90,   10,    0,  -10, 'centerbottom');
test("Angle 105", 105,   10,   -2,   -9, 'centerbottom');
test("Angle 120", 120,   10,   -5,   -8, 'rightbottom');
test("Angle 135", 135,   10,   -7,   -7, 'rightbottom');
test("Angle 150", 150,   10,   -8,   -5, 'rightbottom');
test("Angle 165", 165,   10,   -9,   -2, 'rightcenter');

test("Angle 180", 180,   10,  -10,    0, 'rightcenter');
test("Angle 195", 195,   10,   -9,    2, 'rightcenter');
test("Angle 210", 210,   10,   -8,    5, 'righttop');
test("Angle 225", 225,   10,   -7,    7, 'righttop');
test("Angle 240", 240,   10,   -5,    8, 'righttop');
test("Angle 255", 255,   10,   -2,    9, 'centertop');

test("Angle 270", 270,   10,    0,   10, 'centertop');
test("Angle 285", 285,   10,    2,    9, 'centertop');
test("Angle 300", 300,   10,    5,    8, 'lefttop');
test("Angle 315", 315,   10,    7,    7, 'lefttop');
test("Angle 330", 330,   10,    8,    5, 'lefttop');
test("Angle 345", 345,   10,    9,    2, 'leftcenter');


# ======== End of test cases and error reporting ==========
echo basename(__FILE__) . ": $tests cases, $fails failed.\n";
if (!empty($error)) {
    fprintf(STDERR, "FAIL: $error");
    exit(1);
}
