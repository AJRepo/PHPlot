<?php
# $Id$
/*
     PHPlot Test Suite - Driver Script
     Copyright 2007-2012 lbayuk AT users.sourceforge.net
     Refer to the file LICENSES in the PHPlot test suite for details

There are 3 types of tests: graphic, unit, and error. The purpose of a
graphic test is to produce a plot image. The purpose of a unit test is to
test some internal function. The purpose of an error test is to verify that
error conditions are correctly detected and handled.

Test validation is controlled through a configuration file called: tests.ini
This file is in PHP "ini" format. Each section names a test script, without
the .php extension. For each test, the configuration file contains
instructions to the test driver for validating the test.

(Graphics test generally must be validated by looking at the image,
although if the test fails with an error message the driver will detect
that. Unit tests generally self-validate. Error tests are validated by
checking the error output.)

See the test directory README file for more information about tests.ini
and test script design.

*/

# Name of the product being tested:
define('PRODUCT', 'PHPlot');
# Name of the file containing test validation data:
define('TEST_DATA_FILE', 'tests.ini');
# Name of the test log file, located in the results directory:
define('LOGFILENAME', "test.log");

# Global variables:
$php_exe = '';           # Path to PHP interpreter to be used for testing
$php_version = '';       # Version of PHP being used for testing
$result_dir = '';        # Directory to hold output files
$n_test = 0;             # Number of the current test being run
$total_tests = 0;        # Total number of tests to run
$n_pass = 0;             # Number of tests which passed
$n_skip = 0;             # Number of tests which were skipped
$n_fail = 0;             # Number of tests which failed
$fail_list = array();    # Array (list) of tests which failed
$skip_list = array();    # Array (list) of tests which were skipped
$verbose = 1;            # Verbosity level
$val_data = array();     # Validation data read from TEST_DATA_FILE

# Default values for the test validation data:
$val_data_defaults = array(
    'create_image' => True,
    'exit_ok' => True,
    'exit_error' => False,
    'stderr_empty' => True,
    'stdout_match' => "",
    'stderr_match' => "",
);

# Display script usage and exit:
function usage()
{
    fwrite(STDERR, <<<END
Usage: php run_test.php  script_file... | - | -all | -match patn
  Use '-' to read script filenames from standard input.
  Use -all to run all tests listed in the test configuration file.
  Use -match patn to specify a wildcard match pattern (like shell
     wildcards). This will limit the test to only the matching names.

Environment variables used:
    PHP (required) - points to the PHP CLI program to use for testing.
    RESULTDIR (optional) - directory to store results.

The file 'test.ini' contains validation information about the tests. It
must be found in the current directory.

END
);
    exit(1);
}

# Report a pre-test failure and exit:
function fail($why)
{
    fwrite(STDERR, PRODUCT . " test setup error: $why\n");
    exit(1);
}

# Write a string to both standard output and to the log file.
function lecho($s)
{
    global $log_f;
    fwrite(STDOUT, $s);
    fwrite($log_f, $s);
}

# Write a formatted message to both standard output and to the log file.
# Usage is like printf():  lprintf(format, arg, ...)
function lprintf() // Variable args
{
    $argv = func_get_args();
    $format = array_shift($argv);
    lecho(vsprintf($format, $argv));
}

# Write a message with timestamp in front and newline at end, to both standard
# output and the log file.
# Usage is like printf():  lprintfts(format, arg, ...)
function lprintfts() // Variable args
{
    $argv = func_get_args();
    $format = array_shift($argv);
    $line = vsprintf($format, $argv);
    lecho(strftime('%Y-%m-%d %H:%M:%S') . ' ' . $line . "\n");
}

# Verify global environment and set up for running the tests:
function setup()
{
    global $php_exe, $php_version, $result_dir, $val_data;
    global $log_f, $log_filename;

    # Get environment variables:
    # Environment variables may be available via _ENV or _SERVER, but
    # this will always work:

    # Get path to PHP interpreter to use to run the test scripts.
    # (Can't find any way to have it default to the one we are using.)
    $php_exe = getenv("PHP");
    if (empty($php_exe))
        fail("PHP environment variable is undefined. It must be set\n"
           . " to point to the PHP CLI interpreter program.");

    # Get the version report from the PHP interpreter used for testing. This
    # is not necessarily the same as the current interpreter. This also checks
    # that the supplied PHP env var is valid, and that process execution works,
    # including input/output pipes.
    $fds = array(0 => array('pipe', 'r'),
                 1 => array('pipe', 'w'),
                 2 => array('pipe', 'w'));
    $p = proc_open($php_exe, $fds, $pipes);
    if (!$p)
        fail("Failed to start $php_exe to check version of PHP");
    fwrite($pipes[0], '<?php echo PHP_VERSION;?>');
    fclose($pipes[0]);
    $php_version = fread($pipes[1], 200);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $rval = proc_close($p);
    if ($rval != 0)
        fail("PHP process exited with status: $rval");

    # Get directory where output files will go. Default to 'results'.
    # If it isn't in the environment, add it, so tests can use it.
    $result_dir = getenv("RESULTDIR");
    if (empty($result_dir)) {
        $result_dir = 'results';
        putenv("RESULTDIR=$result_dir");
    }

    # The results directory must not already exist, to prevent overwriting.
    if (file_exists($result_dir)) {
        $message = "Results directory $result_dir";
        if (is_dir($result_dir)) {
            $message .= " already exists.\n"
            . " This would result in output files overwriting previous tests.";
        } else {
            $message .= " exists and is not a directory.";
        }
        fail("$message\nPlease remove the results directory, or use the\n"
           . " RESULTDIR environment variable to point results elsewhere.");
    }
    if (!mkdir($result_dir))
        fail("Failed to create results directory $result_dir");

    # Read the test configuration data:
    if (!is_readable(TEST_DATA_FILE))
        fail("Can't find test configuration file " . TEST_DATA_FILE .  "\n");
    $val_data = parse_ini_file(TEST_DATA_FILE, True);
    if (empty($val_data))
        fail("Unable to read test configuration file " . TEST_DATA_FILE . "\n");

    # Open the log file. Everything from here will be written to stdout
    # and to the log file.
    $log_filename = $result_dir . DIRECTORY_SEPARATOR . LOGFILENAME;
    $log_f = fopen($log_filename, "a");
    if (!$log_f)
        fail("Failed to open log file: $log_filename\n");
}

# Cleanup from testing:
function cleanup()
{
    global $result_dir;
    # Quietly try to remove the result directory, which will only work if
    # it is empty.
    @rmdir($result_dir);
}

# Output the preface text before running tests:
function preface()
{
    global $php_exe, $result_dir, $log_filename, $php_version, $total_tests;
    lecho("====== This is the " . PRODUCT . " Test Suite ======\n");
    lprintfts("Setting up for testing");
    lecho("  Tests will be run using PHP interpreter: $php_exe\n");
    lecho("  PHP interpreter used for testing reports as: PHP $php_version\n");
    lecho("  Result files will be saved in: $result_dir\n");
    lecho("  Testing log will be written to: $log_filename\n");
    $pl = $total_tests == 1 ? '' : 's';
    lprintfts("Testing begins ($total_tests test$pl)");
}

# Output the test result summary:
function summarize($total_run_time)
{
    global $n_pass, $n_fail, $n_skip, $result_dir, $log_filename, $log_f;
    global $fail_list, $skip_list;
    lprintfts("Testing complete - Elapsed time %.2f seconds", $total_run_time);
    lprintf("  Passed:  %3d\n", $n_pass);
    lprintf("  Failed:  %3d\n", $n_fail);
    lprintf("  Skipped: %3d\n", $n_skip);
    if (!empty($fail_list))
        lecho("  Failed tests:\n    "
           . wordwrap(implode(', ', $fail_list)) .  "\n\n");
    if (!empty($skip_list))
        lecho("  Skipped tests:\n    "
           . wordwrap(implode(', ', $skip_list)) .  "\n\n");
    lecho("  Results were saved in: $result_dir\n");
    lecho("  Test log was written to: $log_filename\n");
    fclose($log_f);
}

# Report a failed test:
#   test_name : The short name of the test (base filename).
#   message : The failure message. May be multi-line.
#   runtime : Run-time of the test, in seconds as floating point.
function test_fail($test_name, $message, $runtime = 0.0)
{
    global $n_test, $total_tests, $n_fail, $fail_list;
    lprintfts("%-24s => [ERROR] (%04d/%04d) %6.3f sec%s",
        $test_name, $n_test, $total_tests, $runtime,
        empty($message) ? '' : "\n$message");
    $n_fail++;
    $fail_list[] = $test_name;
}

# Report a passing test:
#   test_name : The short name of the test (base filename).
#   message : Test info message, may be empty.
#   runtime : Run-time of the test, in seconds as floating point.
function test_pass($test_name, $message, $runtime)
{
    global $n_test, $total_tests, $n_pass;
    lprintfts("%-24s => [OK]    (%04d/%04d) %6.3f sec%s",
        $test_name, $n_test, $total_tests, $runtime,
        empty($message) ? '' : "\n$message");
    $n_pass++;
}

# Report a skipped test:
#   test_name : The short name of the test (base filename).
#   message : Message from the test, explaining why it was skipped.
#   runtime : Run-time of the test, in seconds as floating point (ignored)
function test_skip($test_name, $message, $runtime)
{
    global $n_test, $total_tests, $n_skip, $skip_list;
    lprintfts("%-24s => [SKIP]  (%04d/%04d)%s",
        $test_name, $n_test, $total_tests,
        empty($message) ? '' : "\n$message");
    $n_skip++;
    $skip_list[] = $test_name;
}

# Test helper: Run command line and check for status.
# If the script won't run, return False after storing a message.
# If the script ran, return True, but if the script returned an error status
# than append a message to $error.
# Note: return True means the test ran (pass, fail, or skip). Return False
# means the test did not run (so don't bother checking the output).
# The actual test's exit status is stored in $rval.
function run_command($cmd, $script_file, $output_file, $error_file,
    &$error, &$rval)
{
    # Set up process streams. stdin is unused. (Wanted to connect it to
    # php://stdin but that results in an odd 'can't seek on pipe' warning.)
    $fds = array(
        0 => array('pipe', 'r'),                # Unused pipe for stdin
        1 => array('file', $output_file, 'w'),  # Set stdout to the output file
        2 => array('file', $error_file,  'w')); # Set stderr to the error file
    $p = proc_open($cmd, $fds, $pipes);
    if (!$p) {
        $error .= "Failed to execute test $script_file";
        return False;
    }
    fclose($pipes[0]);

    # Close the process and wait for exit, then check exit status:
    $rval = proc_close($p);
    if ($rval != 0)
        # Note: distinguishing 'fail' and 'skip' status is done by caller.
        $error .= "Test returned error status: $rval\n";
    return True;
}

# Test helper: Check for output or error file. If it exists and
# is not empty, return its contents.
# Remove the file if it was empty.
function check_file($filename)
{
    $result = '';
    if (file_exists($filename)) {
        if (filesize($filename) > 0) {
            $result = trim(file_get_contents($filename));
        } else {
            unlink($filename);
        }
    }
    return $result;
}

# Test helper routine: Validate and rename an image file.
#  If filename is not a valid image file, return False.
#  Else, rename the file to have the usual extension, and return True.
function rename_imagefile($filename)
{
    if (!file_exists($filename))
        return False;

    $ii = @getimagesize($filename);
    if (empty($ii))
        return False;

    # Check image type:
    switch($ii[2]) {
        case IMAGETYPE_GIF:
            $ext = '.gif';
            break;
        case IMAGETYPE_PNG:
            $ext = '.png';
            break;
        case IMAGETYPE_JPEG:
            $ext = '.jpg';
            break;
        default:
            return False;
    }

    # Rename the file:
    # We know the extension is .out, but we aren't supposed to, so do it
    # the hard way. Also don't rely on PHP52's pathinfo 'filename' element.
    extract(pathinfo($filename), EXTR_PREFIX_ALL, 'p');
    if (!isset($p_extension)) {
        $to_name = $filename . $ext;
    } else {
        $to_name = $p_dirname . DIRECTORY_SEPARATOR
                   . basename($p_basename, ".$p_extension") . $ext;
    }
    return rename($filename, $to_name);
}

# Get the test validation data for a test, and apply defaults.
# Returns an array with the settings from tests.ini plus defaults.
# A warning is displayed if the test is not in the file.
function get_test_validation($test_name)
{
    global $val_data, $val_data_defaults;

    if (!array_key_exists($test_name, $val_data)) {
        fwrite(STDERR, "Warning: No validation data for '$test_name'\n");
        return $val_data_defaults;
    }
    $r = array_merge($val_data_defaults, $val_data[$test_name]);
    # Resolve some overrides in the data:
    # ... Match pattern for stderr means stderr won't be empty.
    if (!empty($r['stderr_match'])) $r['stderr_empty'] = False;
    # ... Match pattern for stdout means it won't create an image.
    if (!empty($r['stdout_match'])) $r['create_image'] = False;
    # ... Expecting an error exit means not expected an OK exit:
    if ($r['exit_error']) $r['exit_ok'] = False;
    return $r;
}

# Run a test script. The validation data explains what to check for.
function run_test($test_name, $script_file, $output_file, $error_file)
{
    global $php_exe, $result_dir;

    $error = '';
    $message = '';

    # The 'done flag' file is used to determine if a script did exit(),
    # or just returned/fell off the end like it should.
    # This was originally added to detect exit() from inside the library.
    $done_file = $result_dir . DIRECTORY_SEPARATOR . $test_name . '.zzz';
    @unlink($done_file);

    # We need to run the script, then touch the 'done_file', to be able
    # to check for exit().
    # Force error reporting level to the highest value for the tests.
    $phpcmd = "error_reporting(E_ALL|E_STRICT); require '$script_file'; "
            . "touch('$done_file');";
    $cmd = "$php_exe -r \"$phpcmd\"";
    
    # Run the test command. False return means abort, True means the
    # the script ran (although it might have failed, or be a skipped test).
    $start_time = microtime(TRUE);
    if (!run_command($cmd, $script_file, $output_file, $error_file,
                     $error, $rval)) {
        test_fail($test_name, $error);
        return;
    }
    $runtime = microtime(TRUE) - $start_time;

    # Check for skipped test:
    if ($rval == 2) {
        # Ignore $error from run_command, as this was not a test error.
        # Get skip reason from stdout - it should be one short line.
        $message = "  " . check_file($output_file);
        test_skip($test_name, $message, $runtime);
        @unlink($done_file); # Cleanup
        check_file($error_file); # Cleanup
        return;
    }

    # If $error is not empty, it represents a return status.
    if (!empty($error))
      $message .= $error;

    # Output file might be an image, or text.
    # If image, rename, else if text, capture it and show it.
    # Remember if it made an image file, for validation.
    if (rename_imagefile($output_file)) {
        $output_text = '';
        $made_image_file = True;
    } else {
        $output_text = check_file($output_file);
        if (!empty($output_text))
            $message .= "Test standard output:\n------\n" . $output_text
                     . "\n======\n";
        $made_image_file = False;
    }

    # Collect error output.
    $error_text = check_file($error_file);
    if (!empty($error_text))
        $message .= "Test error output:\n------\n" . $error_text . "\n======\n";

    # Get the validation data for the test. (This warns if there isn't any,
    # then provides the defaults.)
    $vd = get_test_validation($test_name);

    # Validate the test based on the configuration settings:
    $failures = array();

    # Was it supposed to make an image, and did it?
    if ($vd['create_image'] && ! $made_image_file)
        $failures[] = "Didn't make an image file";
    elseif (!$vd['create_image'] && $made_image_file)
        $failures[] = "Made an unexpected image file";

    # Was the exit status as expected?
    if ($vd['exit_ok'] && $rval != 0)
        $failures[] = "Expected successful return but got error return";
    elseif ($vd['exit_error'] && $rval == 0)
        $failures[] = "Expected error return but got succesful return";

    # Was anything written to standard error?
    if ($vd['stderr_empty'] && !empty($error_text))
        $failures[] = "Unexpected output written to standard error stream";

    # Does its standard error text match the pattern, if provided?
    # Note: Delimeter is \x01. Modifiers are: i (case insensitive),
    # and s (let . match newline, for multiline spanned matches).
    if (!empty($vd['stderr_match']) &&
            !preg_match("\x01{$vd['stderr_match']}\x01is", $error_text))
        $failures[] = "Standard error did not match the expected text";

    # Does its standard output text match the pattern, if provided?
    # (Note this will always be empty if create_image is True.)
    if (!empty($vd['stdout_match']) &&
            !preg_match("\x01{$vd['stdout_match']}\x01is", $output_text))
        $failures[] = "Standard output did not match the expected text";

    # Check for 'done' flag file to make sure it didn't exit inside.
    # But only report it as a failure if the test was expected to exit OK.
    if (file_exists($done_file))
        unlink($done_file);
    elseif ($vd['exit_ok'])
        $failures[] = "Script exited and did not return properly";

   # Final test status:
   if (empty($failures)) {
       test_pass($test_name, $message, $runtime);
   } else {
       test_fail($test_name, $message . "Validation failure(s):\n - "
                 . implode("\n - ",  $failures), $runtime);
   }
}

# Run an individual test script. This builds the filenames for the output
# and error streams, and calls a function to perform the test.
function do_test($filename)
{
    global $result_dir, $verbose, $n_test, $n_pass, $n_fail;

    # Get the test name (xyz) from the filename (/path/to/xyz.ext).
    # This is also used to build the stdout and stderr names.
    # Note: PATHINFO_FILENAME was added in PHP-5.2.
    $test_name = pathinfo($filename, PATHINFO_FILENAME);

    # Make sure the test script exists.
    if (!is_readable($filename)) {
        test_fail($test_name, "Script not found: $filename\n");
        return;
    }

    $output_file = $result_dir . DIRECTORY_SEPARATOR . $test_name .  '.out';
    $error_file  = $result_dir . DIRECTORY_SEPARATOR . $test_name .  '.err';

    if ($verbose > 1)
        lecho("Test: $test_name\n"
           . "  Script: $filename\n"
           . "  Output to: $output_file\n"
           . "  Errors to: $error_file\n");

    # Run the test, and handle the result (pass, fail, or skip):
    $n_test++;
    run_test($test_name, $filename, $output_file, $error_file);
}

# Main: Process all tests and options on the command line.
#   -all means all tests from config. - means read names from stdin.
#   A -match pattern can be used to limit tests.
if ($argc <= 1) usage();
setup();
$match_pattern = '';
$tests_to_run = array(); // Will contain filenames (testname.php)
for ($arg = 1; $arg < $argc; $arg++) {
    $name = $argv[$arg];
    if ($name == '-') {
        while (($line = fgets(STDIN)) !== False)
            if (($filename = trim($line)) != '') $tests_to_run[] = $filename;
    } elseif ($name == '-all') {
        # Each 'section' in the config file becomes a key in the array.
        # The section name plus .php is the test script name.
        foreach (array_keys($val_data) as $test_name)
            $tests_to_run[] = $test_name . '.php';
    } elseif ($name == '-match') {
        if (++$arg >= $argc) break;
        $match_pattern = $argv[$arg];
    } else {
        $tests_to_run[] = $name;
    }
}
# Apply a match pattern (-match pattern) to limit the tests to run:
if (!empty($match_pattern)) {
    $tests_to_run = array_values(array_filter($tests_to_run,
                 create_function('$s',
                     "return fnmatch('$match_pattern', \$s);")));
}
$total_tests = count($tests_to_run);
preface();
$start_time = microtime(TRUE);
foreach ($tests_to_run as $name) do_test($name);
summarize(microtime(TRUE) - $start_time);
cleanup();
exit(0);
