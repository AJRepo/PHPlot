<?php
# $Id$
# PHPlot unit test: Format Label with extended printf
require_once 'phplot.php';
require_once 'usupport.php';

# True to report test cases and all results:
$test_verbose = False;

# Global test case counters:
$n_tests = 0;
$n_pass = 0;
$n_fail = 0;
$n_skip = 0;

# Extend PHPlot class to allow access to label formatting function:
class PHPlot_test extends PHPlot
{
    // Set label type. Arbitrarily uses 'x' label formatting.
    function set_label_type() // Variable args
    {
        $argv = func_get_args();
        return $this->SetLabelType('x', $argv);
    }

    // Format a label. Does not support custom format w/extra args.
    function format_label($value) // Variable args
    {
        return $this->FormatLabel('x', $value);
    }
}

# ====== Test functions ======

# Check if multiple printf formats is supported, and skip cases if not.
function check_supported($cases)
{
    global $test_verbose, $n_tests, $n_skip;

    # Make sure PHPlot supports multiple printf formats:
    if (PHPlot::version_id < 60200) {
        $n_cases = count($cases) / 2;
        if ($test_verbose)
            echo "Skipping $n_cases cases due to unsupported feature\n";
        $n_tests += $n_cases;
        $n_skip += $n_cases;
        return FALSE;
    }
    return TRUE;
}

# Common bottom of test*() functions
# $cases is an array with pairs of 'input' and 'expected' values.
function test_cases($plot, $cases)
{
    global $test_verbose, $n_tests, $n_pass, $n_fail;

    reset($cases);
    while (list(, $input) = each($cases)) {
        list(, $expected) = each($cases);
        $n_tests++;
        $title = "  Case $n_tests: format '$input'";
        $error = '';
        $result = $plot->format_label($input);
        if ($test_verbose) echo "$title => '$result'\n";
        if (expect_equal($expected, $result, $title, $error)) {
            $n_pass++;
        } else {
            $n_fail++;
            echo "$error\n";
        }
    }
}

# Test printf formatting with 1 format string.
# $cases is an array with input => expected_result.
function test1($format, $cases)
{
    global $test_verbose;

    if ($test_verbose)
        echo "Testing: Single printf format '$format'\n";
    $p = new PHPlot_test;
    $p->set_label_type('printf', $format);
    test_cases($p, $cases);
}

# Test printf formatting with 2 format strings, any number of values.
# $cases is an array with input => expected_result.
function test2($format1, $format2, $cases)
{
    global $test_verbose;

    # Make sure PHPlot supports multiple printf formats:
    if (!check_supported($cases)) return;

    if ($test_verbose)
        echo "Testing: Double printf formats (>=0) '$format1', (<0) '$format2'\n";
    $p = new PHPlot_test;
    $p->set_label_type('printf', $format1, $format2);
    test_cases($p, $cases);
}

# Test printf formatting with 3 format strings, any number of values.
# $cases is an array with input => expected_result.
function test3($format1, $format2, $format3, $cases)
{
    global $test_verbose;

    # Make sure PHPlot supports multiple printf formats:
    if (!check_supported($cases)) return;

    if ($test_verbose)
        echo "Testing: Triple printf formats (>0) '$format1', (<0) '$format2',",
             " (=0) '$format3'\n";
    $p = new PHPlot_test;
    $p->set_label_type('printf', $format1, $format2, $format3);
    test_cases($p, $cases);
}


# ===== Test Cases =====

test1('%4d', array(
      -10,  " -10",
       -1,  "  -1",
        0,  "   0",
        1,  "   1",
       10,  "  10",
    10000,  "10000",
     M_PI,  "   3",
));

test2('%d', '(%d)', array(
      -10,  "(10)",
       -1,  "(1)",
        0,  "0",
        1,  "1",
       10,  "10",
     M_PI,  "3",
));

test3('%.2f', '*-%.2f', 'ZERO', array(
      -10,  "*-10.00",
       -1,  "*-1.00",
        0,  "ZERO",
        1,  "1.00",
       10,  "10.00",
     M_PI,  "3.14",
 -M_SQRT2,  "*-1.41",
));

test1('<%.2e>', array(
 91234.56,  "<9.12e+4>",
-91234.56,  "<-9.12e+4>",
  0.00456,  "<4.56e-3>",
 -0.00456,  "<-4.56e-3>",
     M_PI,  "<3.14e+0>",
 -M_SQRT2,  "<-1.41e+0>",
        0,  "<0.00e+0>",
));

test2('%.2e', '(%.2e)', array(
 91234.56,  "9.12e+4",
-91234.56,  "(9.12e+4)",
  0.00456,  "4.56e-3",
 -0.00456,  "(4.56e-3)",
     M_PI,  "3.14e+0",
 -M_SQRT2,  "(1.41e+0)",
        0,  "0.00e+0",
));

test3('POS %.3e', 'NEG %.3e', 'Z', array(
 91234.56,  "POS 9.123e+4",
-91234.56,  "NEG 9.123e+4",
  0.00456,  "POS 4.560e-3",
 -0.00456,  "NEG 4.560e-3",
     M_PI,  "POS 3.142e+0",
 -M_SQRT2,  "NEG 1.414e+0",
        0,  "Z",
));

# ======== End of test cases and error reporting ==========

echo basename(__FILE__),
    " results: $n_tests test cases,",
    " $n_pass pass, $n_skip skipped, $n_fail fail\n";
if ($n_fail > 0) exit(1);
# PHPlot test suite requires falling off the end, not exit, on success.
