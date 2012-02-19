<?php
# $Id$
# compare_tests : Check test output from PHPlot testing

# Display usage and exit:
function usage()
{
    fwrite(STDERR, '
Usage: compare_tests [options] result_dir reference_dir
  result_dir is the directory where the test results can be found.
  reference_dir is the directory with saved reference output.
  Options:
     -a   View all file pairs
     -d   View all differing pairs
     -n   View all new pairs
   Also -d -n, or -dn or -nd views new or differing pairs.
');
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

# View an image file or pair of image files:
function view_file($filename, $refname = NULL)
{
    global $viewer;
    # Skip non-image files:
    if (!preg_match('/\\.(png|gif|jpg)$/i', $filename)) return;

    if (empty($refname)) {
        echo "View: $filename\n";
        system("$viewer $filename");
    } else {
        echo "View: $refname $filename\n";
        system("$viewer $refname $filename");
    }
}

# If a viewer is defined in the envronment, use that:
if (($viewer = getenv('VIEWER')) === False) {
    # Happens to be my preference:
    $viewer = 'qiv -p';
}

# Default options:
$view_all = False; # -a option
$view_new = False; # -n option
$view_dif = False; # -d option
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

# Get a sorted list of image files and text output files from the outdir:
$o_list = array();
$d = opendir($outdir);
if (!$d) die("Failed to open directory: $outdir\n");
while (($filename = readdir($d)) !== False) {
    if (preg_match('/\\.(png|gif|jpg|out)$/i', $filename))
        $o_list[] = $filename;
}
sort($o_list);

# Process each file in the outdir:
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
        echo "  $filename: Matches\n";
        if ($view_all)
            view_file($srcname);

    } else {
        $n_diff++;
        $s_diff[] = $filename;
        echo "! $filename: Output differs\n";
        if ($view_all || $view_dif)
            view_file($srcname, $refname);
    }
}
echo "Same: $n_same,  New: $n_new,  Differ: $n_diff\n";
if (count($s_diff) > 0)
    echo "\nFiles that differ: " . implode(', ', $s_diff) . "\n";
if (count($s_new) > 0)
    echo "\nFiles that are new: " . implode(', ', $s_new) . "\n";
