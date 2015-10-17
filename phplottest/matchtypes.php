<?php
# $Id$
# PHPlot unit test: Matching plot types with 1 data set.
# This tests that the following plot type pairs make identical plots when
# there is only a single data set:
#   'area' and 'stackedarea'
#   'bars' and 'stackedbars'
require_once 'phplot.php';


// Make a data array, with random but repeatable results:
mt_srand(16); // Any number will do
$data = array();
for ($i = 0; $i < 50; $i++) {
    $data[] = array('', mt_rand(0, 1000) / 10.0);
}

// Wrapper for simple plot type test:
class testplot extends PHPlot_truecolor
{
    function __construct($plot_type, $data, $width = 800, $height = 800)
    {
        global $data;
        parent::__construct($width, $height);
        $this->SetDataType('text-data');
        $this->SetDataValues($data);
        $this->SetPlotType($plot_type);
        $this->SetPrintImage(False);
        $this->DrawGraph();
    }
}

function compare_plots($type1, $type2)
{
    global $data, $n_fail;

    $title = "Single data set plot compare: $type1 vs $type2:";
    $p1 = new testplot($type1, $data);
    $p2 = new testplot($type2, $data);
    if ($p1->EncodeImage('raw') != $p2->EncodeImage('raw')) {
        echo "$title failed\n";
        return FALSE;
    }
    echo "$title OK\n";
    return TRUE;
}

$failed = 0;
if (!compare_plots('area', 'stackedarea')) $failed++;
if (!compare_plots('bars', 'stackedbars')) $failed++;
if ($failed > 0) exit(1);
