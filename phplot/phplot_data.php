<?php
/* $Id$
 * 
 * Copyright (C) 2000 Afan Ottenheimer.  Released under
 * the GPL and PHP licenses as stated in the the README file which
 * should have been included with this document.

 * This is an subclass for phplot.php and should only be
 * called after phplot.ini has been called. This extends
 * phplot by adding additional routines that can be used
 * to modify the data arrays.
 *
 * XXX data_values must be a *numerical* array, this could be enforced in SetDataValues() XXX
 */

class PHPlot_Data extends PHPlot 
{
    /*!
     * Constructor
     */
    function PHPlot_Data()
    { 
        if (! isset($this->img)) { 
            $this->PHPlot();
        }
    }
    
    /*!
     * Will scale all data rows
     * Maybe later I will do a function that only scales some rows
     * if $even is TRUE, data will be scaled with "even" factors. 
     * \note Original code by Thiemo Nagel
     */
    function DoScaleData($even, $show_in_legend) 
    {
        $offset = 0;        // We use this not to read labels in text-data
            
        if ($this->data_type == 'text-data') {
            $offset = 1;
        } elseif ($this->data_type != 'data-data') {
            $this->DrawError('wrong data type!!');
            return FALSE;
        }

        // Determine maxima for each data row in array $max
        // Put maximum of the maxima in $maxmax
        $maxmax = 0;
        for($i=0; $i < $this->num_data_values); $i++) {
            $rowsize = count($this->data_values[$i]);
            for ($j=$offset; $j < $rowsize; $j++) {
                if ($this->data_values[$i][$j] > $max[$j])
                    $max[$j] = $this->data_values[$i][$j];
                if ($max[$j] > $maxmax) 
                    $maxmax = $max[$j];
            }
        }
        
        // determine amplification factor $amplify
        $end = count($max) + $offset;
        for ($i=$offset; $i < $end; $i++) {
            if ($max[$i] == 0 || $max[$i] == $maxmax) {
                $amplify[$i] = 1;  // no divide by zero
            } else {
                if ($even) {
                    $amp = pow(10,round(log10($maxmax / $max[$i]))-1);
                    if ($amp * $max[$i] * 5 < $maxmax) {
                        $amp *= 5;
                    } elseif ($amp * $max[$i] * 2 < $maxmax) {
                        $amp *= 2;
                    }
                } else {
                    $amp = $maxmax / $max[$i];
                    $digits = floor(log10($amp));
                    $amp = round($amp/pow(10,$digits-1))*pow(10,$digits-1);
                }
                $amplify[$i] = $amp;
            }
            if ($amplify[$i] != 1 && $show_in_legend) 
                $this->legend[$i] .= "*$amplify[$i]";
        }

        // Amplify data
        // On my machine, running 1000 iterations over 1000 rows of 12 elements each,
        // the for loops were 43.2% faster (MBD)
        for ($i = 0; $i < $this->num_data_values; $i++) {
            $rowsize = count($this->data_values[$i]);
            for ($j=$offset; $j < $rowsize; $j++) {
                $this->data_values[$i][$j] *= $amplify[$j];
            }
        }

        //Re-Scale Vertical Ticks if not already set
        if ( !$this->vert_tick_increment) {
            $this->SetVertTickIncrement("") ;
        }

        return TRUE;
    } //function DoScaleData


    /*!
     * Computes a moving average of strength $interval for
     * data row number $datarow, where 0 denotes the first
     * row of y-data. 
     * \note Original idea by Theimo Nagel
     */
    function DoMovingAverage($datarow, $interval, $show_in_legend)
    {
        if ($interval == 0) {
            $this->DrawError('DoMovingAverage(): interval can\'t be 0');
            return FALSE;
        }

        if ($this->data_type == 'text-data') {
            $datarow++;
        } elseif ($this->data_type != 'data-data') {
            $this->DrawError('DoMovingAverage(): wrong data type!!');
            return FALSE;
        }
        
        if ($show_in_legend) {
            $this->legend[$datarow] .= " (MA: $interval)";
        }

        for ($i=0;$i < $this->num_data_values; $i++) {
            $storage[$i % $interval] = @ $this->data_values[$i][$datarow];
            $ma = array_sum($storage);
            $ma /= count($storage);
            $this->data_values[$i][$datarow] = $ma;
        }
        return TRUE;
    } //function DoMovingAverage

    /*!
     * Computes an exponentially smoothed moving average
     * $perc is the "smoothing percentage"
     */
    function DoExponentialMovingAverage($datarow, $perc, $show_in_legend)
    {
        if ($this->data_type == 'text-data') {
            $datarow++;
        } elseif ($this->data_type != 'data-data') {
            $this->DrawError('DoWeightedMovingAverage(): wrong data type!!');
            return FALSE;
        }
        
        if ($show_in_legend) {
            $this->legend[$datarow] .= " (MA: $interval)";
        }

        $storage[0] = $this->data_values[0][$datarow];
        for ($i=1;$i < $this->num_data_values; $i++) {
            $storage[$i] = @ $storage[$i-1] + $perc * ($this->data_values[$i][$datarow] - $storage[$i-1]);
            $ma = array_sum($storage);
            $ma /= count($storage);
            $this->data_values[$i][$datarow] = $ma;
        }
        return TRUE;
   
    }
    /*!
     * Removes the DataSet of number $index
     */
    function DoRemoveDataSet($index) 
    {
        $offset = 1;
        if ($this->data_type == 'data-data') {
            $offset++;
        } elseif ($this->data_type != 'text-data') {
            $this->DrawError('wrong data type!!');
            return FALSE;
        }
    
        $index += $offset;
        foreach ($this->data_values as $key=>$val) {
            foreach ($val as $key2=>$val2) {
                if ($key2 >= $index) {
                    if (isset($this->data_values[$key][$key2+1])) {
                        $this->data_values[$key][$key2] = $this->data_values[$key][$key2+1];
                    } else {
                        unset($this->data_values[$key][$key2]);
                    }
                }
            }
        }
    } // function DoRemoveDataSet
    
    
    /*!
     * Computes row x divided by row y, stores the result in row x
     * and deletes row y
     */
    function DoDivision($x,$y) 
    {
        $offset = 1;
        if ($this->data_type == 'data-data') {
            $offset++;
        } elseif ($this->data_type != 'text-data') {
            $this->DrawError('wrong data type!!');
            return FALSE;
        }
    
        $x += $offset; $y += $offset;
        reset($this->data_values);
        while (list($key, $val) = each($this->data_values)) {
            if ($this->data_values[$key][$y] == 0) {
                $this->data_values[$key][$x] = 0;
            } else {
                $this->data_values[$key][$x] /= $this->data_values[$key][$y];
            }
        }
    
        $this->DoRemoveDataSet($y-$offset);
    } // function DoDivision

} // class PHPlot_Data extends PHPlot
?>
