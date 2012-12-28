<?php
# $Id$
# Testing phplot - Version constants (PHPlot>=5.4.0)
# This is a unit test. No image is produced.
require_once 'phplot.php';
$failure = '';

# Check: Constant is defined.
if (!defined('PHPlot::version')) {
    $failure .= "phplot does not define a version constant\n";

# Check: Format #.#.#...
} elseif (!preg_match('/^(\\d+\\.\\d+\\.\\d+)/', PHPlot::version, $v)) {
    $failure .= "phplot version constant is not in the expected format\n";
} else {
    # These checks are only if the version constant looks right.

    # Read the top ~10 lines of the source, looking for the title.
    # This needs to somewhat match the version constant.
    # Open the source file, using the INCLUDE path.
    $f = fopen('phplot.php', 'r', TRUE);
    if (!$f) exit(1); // Error already reported by fopen. Fatal.
    $found = False;
    for ($i = 0; $i < 10; $i++) {
        $s = fgets($f);
        if ($s === False) break;
        if (preg_match('/PHPLOT Version +(\\d+\\.\\d+\\.\\d+)/i', $s, $m)) {
            $found = True;
            break;
        }
    }
    fclose($f);
    if (!$found) {
        $failure .= "Unable to read version comment from source file\n";
    } elseif ($m[1] != $v[1]) {
        $failure .= "File comment version {$m[1]} != version constant {$v[1]}\n";
    }
}

# This was added in 6.0.0, so don't fail the test if it is missing.
if (defined('PHPlot::version_id')) {
    $v_s = ((int)(PHPlot::version_id / 10000)) . '.' .
           ((int)(PHPlot::version_id / 100) % 100) . '.' . 
           (int)(PHPlot::version_id % 100);

    if ($v_s != $v[1]) {
        $failure .= "Mismatch between version: " . PHPlot::version . "\n"
                  . "          and version_id: " . PHPlot::version_id . "\n";
    }
} else {
    echo "Note: PHPlot version_id constant is not defined\n";
}

if (!empty($failure)) {
    fwrite(STDERR, "Version test failed:\n$failure\n");
    exit(1); // Test framework says: exit on error, fall off if pass.
}

echo "Pass: PHPlot version = " . PHPlot::version . "\n";
