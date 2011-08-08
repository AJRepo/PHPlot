<?php
# $Id$
# Testing phplot - Version constant (PHPlot>=5.4.0)
# This is a unit test. No image is produced.
require_once 'phplot.php';
$failure = '';

# Check: Constant is defined.
if (!defined('PHPlot::version')) {
    $failure .= "phplot does not define a version constant\n";

# Check: Format #.#.#...
} elseif (!preg_match('/^(\\d+\\.\\d+\\.\\d)(.*)/', PHPlot::version, $v)) {
    $failure .= "phplot version constant is not in the expected format\n";
} else {
    # These checks are only if the version constant looks right.

    # Read the top ~10 lines of the source, looking for the title.
    # This needs to somewhat match the version constant.
    # Open the source file, using the INCLUDE path.
    $f = fopen('phplot.php', 'r', TRUE);
    if (!$f) exit(1); // Error already reported by fopen. Fatal.
    $found = '';
    for ($i = 0; $i < 10; $i++) {
        $s = fgets($f);
        if ($s === False) break;
        if (preg_match('/PHPLOT Version/i', $s)) {
            $found = $s;
            break;
        }
    }
    fclose($f);
    if (!$found) {
        $failure .= "Unable to read version comment from source file\n";
    } else {
        if (!preg_match('/PHPLOT Version (\\d+\\.\\d+\\.\\d+)(.*)/',
                        $found, $m)) {
            $failure .= "Unable to match version comment in source file\n";
        } else {
            if ($m[1] != $v[1]) {
                $failure .= "File comment version {$m[1]} != version constant {$v[1]}\n";
            }
            # Check: both say CVS or neither says CVS:
            if (preg_match('/CVS/', $m[2]) != preg_match('/CVS/', $v[2])) {
                $failure .= "Disagree on CVS status:\n"
                          . "    File comment: {$m[2]}\n"
                          . "Version constant: {$v[2]}\n";
            }
        }
    }
}
if (!empty($failure)) {
    fwrite(STDERR, "Version test failed:\n$failure\n");
    exit(1); // Test framework says: exit on error, fall off if pass.
}
echo "Pass: PHPlot version = " . PHPlot::version . "\n";
