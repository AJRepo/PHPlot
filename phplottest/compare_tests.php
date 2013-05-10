<?php
# $Id$
# compare_tests : Check test output from PHPlot testing

# Default image viewer command. This needs to accept 1 or 2 image file
# names; with 2 names it should allow back/forth toggling.
# The VIEWER environment variable overrides this.
define('DEFAULT_VIEWER', 'qiv -p');

# Default text file compare command.
# The DIFF environment variable overrides this.
define('DEFAULT_DIFF', 'diff -u');

# Display usage and exit:
function usage()
{
    fprintf(STDERR, '
Usage: compare_tests [options] result_dir reference_dir
  result_dir is the directory where the test results can be found.
  reference_dir is the directory with saved reference output.
  Options:
     -a   View all file pairs
     -d   View all differing pairs
     -n   View all new pairs
     -q   Quiet mode: do not list matching tests
     -m P Check only tests matching wildcard match pattern P
   Also -d -n, or -dn or -nd views new or differing pairs.
   The VIEWER environment variable defines the command line for an image
file viewer. The default is: %s
   The DIFF environment variable defines the command line for text
file comparison. The default is: %s
',  DEFAULT_VIEWER, DEFAULT_DIFF);
    exit(1);

}

# Compare two files, byte for byte, return True if match else False.
# The files (images) aren't too big, so just read the whole thing in
# to memory and compare.
function compare_files($file1, $file2)
{
  return (($s1 = file_get_contents($file1)) !== False
       && ($s2 = file_get_contents($file2)) !== False
       && $s1 === $s2);
}

# Return true if the given filename seems to be an image file:
function is_image_file($filename)
{
    return preg_match('/\\.(png|gif|jpg)$/i', $filename);
}

# View a single image or text file. This is used when there is a new test
# result (no matching reference file), or when the results match but the
# 'view all' option was used.
function view_file($filename)
{
    global $viewer;

    if (is_image_file($filename)) {
        echo "View: $filename\n";
        system("$viewer $filename");
    } else {
        # Text file
        echo "===== Text file: $filename\n";
        readfile($filename);
        echo "=====\n";
    }
}

# View a pair of image or text files. This is used when the test results
# do not match: the reference file differs from the result file.
function view_files($filename, $refname)
{
    global $viewer, $diff;

    if (is_image_file($filename)) {
        echo "View: $refname $filename\n";
        system("$viewer $refname $filename");
    } else {
        # Text file
        echo "===== Compare: $refname $filename\n";
        system("$diff $refname $filename");
        echo "=====\n";
    }
}

# Get the command lines to use for viewer and text file compare:
if (($viewer = getenv('VIEWER')) === False)
    $viewer = DEFAULT_VIEWER;
if (($diff = getenv('DIFF')) === False)
    $diff = DEFAULT_DIFF;

# Default options:
$view_all = False; # -a option
$view_new = False; # -n option
$view_dif = False; # -d option
$be_quiet = FALSE; # -q option
$match_pattern = '';  # -m option
# Parse command arguments:
$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];
if ($argc <= 1) usage();
for ($narg = 1; $narg < $argc; $narg++) {
    $arg = $argv[$narg];
    if ($arg[0] != '-') break;
    if ($arg == '--') {
        $narg++;
        break;
    }
    $nc = strlen($arg);
    for ($ac = 1; $ac < $nc; $ac++) {
        switch ($arg[$ac]) {
            case 'n': $view_new = True; break;
            case 'd': $view_dif = True; break;
            case 'a': $view_all = True; break;
            case 'q': $be_quiet = True; break;
            case 'm':
                if (++$narg >= $argc) usage(); # Missing arg value
                $match_pattern = $argv[$narg];
                break;
            default: usage();
        }
    }
}
# Non-flag arguments are now $argv[$narg : $argc-1]
if ($argc - $narg != 2) usage();
$outdir = $argv[$narg++];
$refdir = $argv[$narg];

$n_same = 0;
$n_new  = 0;
$n_diff = 0;
$s_diff = array();
$s_new = array();
$n_total = 0;   # Total result files, before -m filtering.

# Get a sorted list of image files and text output files from the outdir:
$o_list = array();
$d = opendir($outdir);
if (!$d) die("Failed to open directory: $outdir\n");
while (($filename = readdir($d)) !== False) {
    # Look only at image and .out files:
    if (preg_match('/\\.(png|gif|jpg|out)$/i', $filename)) {
        $n_total++;
        if (empty($match_pattern) || fnmatch($match_pattern, $filename)) {
            $o_list[] = $filename;
        }
    }
}
$n_check = count($o_list);
if ($n_check == 0) {
    fwrite(STDERR, "Error: No matching files to check\n");
    exit(1);
}
sort($o_list);

# Process each result file:
foreach ($o_list as $filename) {
    $srcname = $outdir . DIRECTORY_SEPARATOR . $filename;
    $refname = $refdir . DIRECTORY_SEPARATOR . $filename;
    if (!file_exists($refname)) {
        $n_new++;
        $s_new[] = $filename;
        echo "+ $filename: No reference to compare with (new test)\n";
        if ($view_all || $view_new)
            view_file($srcname);

    } elseif (compare_files($srcname, $refname)) {
        $n_same++;
        if (!$be_quiet)
            echo "  $filename: Matches\n";
        if ($view_all)
            view_file($srcname);

    } else {
        $n_diff++;
        $s_diff[] = $filename;
        echo "! $filename: Output differs\n";
        if ($view_all || $view_dif)
            view_files($srcname, $refname);
    }
}

# Report the results:

echo "\nResults: Same: $n_same,  New: $n_new,  Differ: $n_diff\n";
if (count($s_diff) > 0)
    echo "\nFiles that differ: " . implode(', ', $s_diff) . "\n";
if (count($s_new) > 0)
    echo "\nFiles that are new: " . implode(', ', $s_new) . "\n";
if (($n_filtered_out = $n_total - $n_check) > 0)
    echo "\nWarning: $n_filtered_out result file(s) were filtered out.\n";
