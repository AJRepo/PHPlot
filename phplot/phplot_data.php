<?php
/*
Copyright (C) 2000 Afan Ottenheimer.  Released under
the GPL and PHP licenses as stated in the the README file which
should have been included with this document.

This is an subclass for phplot.php and should only be
called after phplot.ini has been called. This extends
phplot by adding additional routines that can be used
to modify the data arrays.
*/

class PHPlot_Data extends PHPlot {

	function DoScaleData($even, $show_in_legend) {
	// will scale all data rows
	// maybe later I will do a function that only scales some rows
	// if $even is true, Data will be scaled with "even" factors. Submitted by Thiemo Nagel
		$offset = 0;
		unset($max);
		if ($this->data_type == 'text-linear') {
			$offset++;
		} elseif ($this->data_type != 'linear-linear') {
			$this->DrawError('wrong data type!!');
			return false;
		}

		// Determine maxima for each data row in array $max
		reset($this->data_values);
		while (list($key, $val) = each($this->data_values)) {
			$i = 0;
			reset($val);
			while (list($key, $val2) = each ($val)) {
				if ($i >= $offset)
					if ($max[$i-$offset] < $val2) $max[$i-$offset] = $val2;
				$i++;
			}
		}

		// Get maximum of the maxima in $maxmax
		$maxmax = 0;
		while (list($key, $val) = each($max)) {
			if ($maxmax < $val) $maxmax = $val;
		}

/*		reset($max);
		while (list($key, $val) = each($max))
			 echo ("$val<br>");  */

		// determine amplification factor $amplify
		reset($max);
		while (list($key, $val) = each($max)) {
			if ($val == 0 || $val == $maxmax) {
				$amplify[$key] = 1;  // no divide by zero
			} else {
				if ($even) {
					$amp = pow(10,round(log10($maxmax / $val))-1);
					if ($amp*$val*5 < $maxmax) {
						$amp *= 5;
					} elseif ($amp*$val*2 < $maxmax) {
						$amp *= 2;
					}
				} else {
					$amp = $maxmax / $val;
					$digits = floor(log10($amp));
					$amp = round($amp/pow(10,$digits-1))*pow(10,$digits-1);
				}
				$amplify[$key] = $amp;
			}
			if ($amplify[$key] != 1 && $show_in_legend) $this->legend[$key] .= "*$amplify[$key]";

//			echo "amp[$key]: $amplify[$key]<br>";
		}

		// Amplify data
		reset($this->data_values);
		$i = 0;
		while (list($key, $val) = each($this->data_values)) {
			$j = 0;
			reset($val);
			while (list($key, $val2) = each ($val)) {
				if ($j >= $offset)
					$this->data_values[$i][$j] *= $amplify[$j-$offset];
				$j++;
			}
			$i++;
		}

		//Re-Scale Vertical Ticks if not already set
		if ( !$this->vert_tick_increment) {
			$this->SetVertTickIncrement("") ;
		}

		return true;
	} //function DoScaleData

	function DoMovingAverage($datarow, $interval, $show_in_legend) {
		//computes a moving average of strength $interval for
		//data row number $datarow, where 0 denotes the first
		//row of y-data. Submitted by Theimo Nagel

		if ($interval == 0) {
			$this->DrawError('DoMovingAverages: interval can\'t be 0');
			return false;
		}

		if ($show_in_legend) $this->legend[$datarow] .= " (MA: $interval)";

		if ($this->data_type == 'text-linear') {
			$datarow++;
		} elseif ($this->data_type != 'linear-linear') {
			$this->DrawError('DoMovingAverages: wrong data type!!');
			return false;
		}

		reset($this->data_values);
		$i = 0;
		unset($storage);
		while (list($key, $val) = each($this->data_values)) {
			$j = 0;
			reset($val);
			while (list($key, $val2) = each($val)) {
				if ($j == $datarow) {
					$storage[$i % $interval] = $val2;
					$ma = 0; for ($k = 0; $k < $interval; $k++) $ma += $storage[$k];
					$ma /= $interval;
					if ($i < $interval) $this->data_values[$i][$j] = 0;
					else $this->data_values[$i][$j] = $ma;
				}
				$j++;
			}
			$i++;
		}
		return true;
	} //function DoMovingAverage


function DoRemoveDataSet($index) {
// removes the DataSet of number $index

	$offset = 1;
	if ($this->data_type == 'linear-linear') {
		$offset++;
	} elseif ($this->data_type != 'text-linear') {
		$this->DrawError('wrong data type!!');
		return false;
	}

	$index += $offset;
	reset($this->data_values);
	while (list($key, $val) = each($this->data_values)) {
		reset($val);
		while (list($key2, $val2) = each($val)) {
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


function DoDivision($x,$y) {
// computes row x divided by row y, stores the result in row x
// and deletes row y

	$offset = 1;
	if ($this->data_type == 'linear-linear') {
		$offset++;
	} elseif ($this->data_type != 'text-linear') {
		$this->DrawError('wrong data type!!');
		return false;
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
