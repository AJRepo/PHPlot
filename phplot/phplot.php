<?php
/*
Copyright (C) 1998, 1999, 2000 Afan Ottenheimer.  Released under
the GPL and PHP licenses as stated in the the README file which 
should have been included with this document. 

World Coordinates are the XY coordinates relative to the 
axis origin that can be drawn. Not the device (pixel) coordinates
which in GD is relative to the origin at the upper left
side of the image. 
*/

//GLOBAL VARIABLES AND FUNCTIONS
	$color_array = 1;  	//If this is set to 1 then include small list
						//If 2 then include large list
						//If 0 then don't do color name arrays
	//include("rgb_small.inc.php");
	include("rgb.inc.php");

function SetColor($color_asked) { 
	global $ColorArray;
	if ( count($color_asked) == 3 ) { //already array of 3 rgb
	   	$ret_val =  $color_asked;
	} else { // is asking for a color by name
		$ret_val =  $ColorArray[$color_asked];
	}
	return $ret_val;
}

class PHPlot{

	var $image_width  = 600;	//Total Width in Pixels (world coordinates)
	var $image_height = 400; 	//Total Height in Pixels (world coordinates)
	var $x_left_margin;
	var $y_top_margin;
	var $x_right_margin;
	var $y_bot_margin;
	var $plot_area = array(5,5,600,400);
//Fonts
	var $use_ttf  = 1;		  //Use TTF fonts (1) or not (0)
	var $font = "./benjamingothic.ttf";

	var $small_ttffont_size = 12; // 
	//non-ttf
	var $small_font = 2; // fonts = 1,2,3,4 or 5 
	var $small_font_width = 6.0; // width in pixels
	var $small_font_height = 10.0; // height in pixels

	var $title_ttffont = "./benjamingothic.ttf";
	var $title_ttffont_size = 14;
	var $title_angle= 0;
	//non-ttf
	var $title_font = "4";
	var $title_font_width = 8.0; // width in pixels for non-ttf
	var $title_font_height = 12.0; // height in pixels for non-ttf

	var $axis_ttffont = "./benjamingothic.ttf";
	var $axis_ttffont_size = 8;
	var $x_datalabel_angle = 45;
	var $y_label_angle = 90;
	//non-ttf
	var $axis_font = 2;
	var $axis_font_width = 6.0; // width in pixels for non-ttf
	var $axis_font_height = 10.0; // height in pixels for non-ttf
//Formats
	var $file_format = "png";
//Plot Colors
	var $bg_color; 
	var $plot_bg_color;
	var $grid_color;
	var $light_grid_color;
	var $tick_color;
	var $title_color;
	var $text_color;
	var $i_light; 
//Data
	var $data_type = "text-linear"; // text-linear, linear-linear-error, linear-linear, log-linear, log-log, linear-text
	//var $plot_type= "linepoints";	//bars, lines, linepoints, area, dots, pie
	var $plot_type= "linepoints";	//bars, lines, area, dots, pie
	var $point_size = 10;
	var $point_shape = "diamond"; //rect,circle,diamond,triangle,dot,line,halfline
	var $error_bar_shape = "tee"; //tee, line
	var $error_bar_size = 5; //right left size of tee
	var $data_values;

	var $data_color = array("blue","white",array(0,0,0));
	var $data_border_color = array("black");
	var $number_x_points;
	var $plot_min_x; // Max and min of the plot area
	var $plot_max_x; // Max and min of the plot area
	var $plot_min_y; // Max and min of the plot area
	var $plot_max_y; // Max and min of the plot area
	var $min_y;
	var $max_y;
	var $max_x = 10;  //Must not be = 0;
	var $precision = "1";
	var $si_units;
//Labels
	var $title_txt = "this is a title";
	var $y_label_txt = "Millions";
	var $x_label_txt = "years";
	var $x_grid_label_type = "data";
	var $tick_length = "10";  //pixels
	var $draw_y_grid = 1;
	var $num_vert_ticks = "";
	var $vert_tick_increment = "1";  //Set num_vert_ticks or vert_tick_increment, not both.
	var $x_datalabel_maxlength = 20;
	var $line_width = 2;


	function SetUseTTF($which_ttf) {
		$this->use_ttf = $which_ttf;
		return true;
	}

	function SetFileFormat($which_file_format) {
	//eventually test to see if that is supported - if not then return false
		$accepted = "jpg,gif,png";
		$asked = trim($which_file_format);
		if (eregi($asked, $accepted)) {
			$this->file_format = $asked;
			return true;
		} else {
			return false;
		}
	}

	function SetImageArea($which_iw,$which_ih) {
		$this->image_width = $which_iw;
		$this->image_height = $which_ih;
		return true;
	}

	//Set up initial image
	function InitImage() {
		$this->img = ImageCreate($this->image_width, $this->image_height);
		$this->SetDefaultColors();
		return true;
	}

	function SetPrecision($which_prec) {
		$this->precision = $which_prec;
		return true;
	}

	function SetDefaultColors() {
		$this->SetPlotBgColor(array(222,222,222));
		$this->SetBackgroundColor(array(200,222,222)); //can use rgb values or "name" values
		$this->SetTextColor("black");
		$this->SetGridColor("black");
		$this->SetLightGridColor(array(175,175,175));
		$this->SetTickColor("black");
		$this->SetTitleColor(array(0,0,0)); // Can be array or name
		$this->i_light = ImageColorAllocate($this->img,194,194,194);
		$this->i_dark  = ImageColorAllocate($this->img,100,100,100);
	}

	function PrintImage() {
		switch($this->file_format) { 
			case "png":
				Header("Content-type: image/gif");
				ImagePng($this->img);
				break;
			case "jpg":
				Header("Content-type: image/gif");
				ImageJPEG($this->img);
				break;
			case "gif":
				Header("Content-type: image/gif");
				ImageGif($this->img);
				break;
			default:
				echo "Please select an image type!<br>";
				break;	
		}
		ImageDestroy($this->img);
		return true;
	}


	function DrawBackground() {
		if (($this->img) == "") {
			$this->InitImage();
		}
		ImageFilledRectangle($this->img, 0, 0, 
			$this->image_width, $this->image_height, $this->bg_color);
		return true;
	}

	function DrawImageBorder() { 
		ImageLine($this->img,0,0,$this->image_width-1,0,$this->i_light);
		ImageLine($this->img,1,1,$this->image_width-2,1,$this->i_light);
		ImageLine($this->img,0,0,0,$this->image_height-1,$this->i_light);
		ImageLine($this->img,1,1,1,$this->image_height-2,$this->i_light);
		ImageLine($this->img,$this->image_width-1,0,$this->image_width-1,$this->image_height-1,$this->i_dark);
		ImageLine($this->img,0,$this->image_height-1,$this->image_width-1,$this->image_height-1,$this->i_dark);
		ImageLine($this->img,$this->image_width-2,1,$this->image_width-2,$this->image_height-2,$this->i_dark);
		ImageLine($this->img,1,$this->image_height-2,$this->image_width-2,$this->image_height-2,$this->i_dark);
		return true;
	}

	function SetDrawYGrid($which_dyg) { 
		$this->draw_y_grid = $which_dyg;  // 1=true or anything else=false
	}


	function SetXGridLabelType($which_xglt) { 
		$this->x_grid_label_type = $which_xglt;
		return true;
	}

	function SetXLabel($xlbl) { 
		$this->x_label_txt = $xlbl;
		return true;
	}
	function SetYLabel($ylbl) { 
		$this->y_label_txt = $ylbl;
		return true;
	}
	function SetTitle($title) { 
		$this->title_txt = $title;
		return true;
	}

	//function SetLabels($xlbl,$ylbl,$title) { 
	//	$this->title_txt = $title;
	//	$this->x_label_txt = $xlbl;
	//	$this->y_label_txt = $ylbl;
	//}

	function DrawLabels() {
		$this->DrawTitle();
		$this->DrawXLabel();
		$this->DrawYLabel();
		return true;
	}

	function DrawXLabel() {
		ImageString($this->img, $this->small_font, 
			-($this->small_font_width*strlen($this->x_label_txt)/2.0) + $this->xtr(($this->plot_max_x+$this->plot_min_x)/2.0) , 
			($this->ytr($this->plot_min_y) + $this->x_label_height/2), 
			$this->x_label_txt, $this->text_color);

		return true;
	}
	function DrawYLabel() {
   		ImageStringUp($this->img, $this->small_font,8, 
			(($this->small_font_width*strlen($this->y_label_txt)/2.0) + 
				$this->ytr(($this->plot_max_y + $this->plot_min_y)/2.0) ), $this->y_label_txt, $this->text_color);
		return true;
	}



	function DrawTitle() {
		if ($this->use_ttf == 1 ) { 
			$size = $this->TTFBBoxSize($this->title_ttffont_size, $this->title_angle, $this->title_ttffont, $this->title_txt);
			$xpos = $this->plot_area[0] + ($this->plot_area_width /2 ) - $size[0]/2 ;
			$ypos = 2 * $size[1];
			ImageTTFText($this->img, $this->title_ttffont_size, $this->title_angle, 
					$xpos, $ypos, $this->title_color, $this->title_ttffont, $this->title_txt);
//echo "DEBUG: $xpos, $ypos, $this->img, $this->title_ttffont_size, $this->title_angle, $this->title_color, $this->title_ttffont, $this->title_txt<br>";
//exit;
		} else { 
			ImageString($this->img, $this->title_font,
				($this->plot_area[0] + $this->plot_area_width / 2) - (strlen($this->title_txt) * $this->title_font),
				2*$this->title_font_height,
				$this->title_txt,
				$this->title_color);
		}
		return true;

	}

	function DrawPlotAreaBackground() {
		ImageFilledRectangle($this->img,$this->plot_area[0],
			$this->plot_area[1],$this->plot_area[2],$this->plot_area[3],
			$this->plot_bg_color);
	}

	function SetBackgroundColor($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->bg_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}
	function SetPlotBgColor($which_color) { 
		list($r, $g, $b) = SetColor($which_color);
		$this->plot_bg_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetTitleColor($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->title_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetTickColor ($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->tick_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetTextColor ($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->text_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetLightGridColor ($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->light_grid_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetGridColor ($which_color) { 
		if (($this->img) == "") {
			$this->InitImage();
		}
		list($r, $g, $b) = SetColor($which_color);
		$this->grid_color=ImageColorAllocate($this->img, $r, $g, $b);
		return true;
	}

	function SetCharacterHeight() { 
		//to be set
		return true;
	}

	function SetPlotType($which_pt) { 
		$accepted = "bars,lines,linepoints,area,dots,pie";
		$asked = trim($which_pt);
		if (eregi($asked, $accepted)) {
			$this->plot_type = $which_pt;
			return true;
		} else {
			return false;
		}
	}

	function FindDataLimits() {
		//bar charts are a bit different than regular graphs. For them what
		// we have, instead of X values, is # of records equally spaced on data. 
		//Bar and Column chart data is passed in as $data[] = (title,y1,y2,y3,y4,...)
		//Regular X-Y data is passed in as $data[] = (title,x,y1,y2,y3,y4,...) which you can use for multi-dimentional plots.

		$this->number_x_points = count($this->data_values);

		switch ($this->data_type) {
			case "text-linear":
				$minx = 0; //valid for BAR CHART TYPE GRAPHS ONLY
				$maxx = $this->number_x_points - 1 ;  //valid for BAR CHART TYPE GRAPHS ONLY
				$miny = (double) $this->data_values[0][1];
				$maxy = $miny;
			break;
			default:  //Everything else: linear-linear, etc. 
				$maxx = $this->data_values[0][1];
				$minx = $maxx;
				$miny = $this->data_values[0][2];
				$maxy = $miny;
				$maxy = $miny;
			break;
		}

		$max_records_per_group = 0;
		$total_records = 0;

		reset($this->data_values);
		while (list(, $dat) = each($this->data_values)) {  //for each X barchart setting
		//foreach($this->data_values as $dat)  //can use foreach only in php4

			$tmp = 0;
			$total_records += count($dat) - 1; // -1 for label

			switch ($this->data_type) {
				case "text-linear":
					//Find the relative Max and Min

					while (list($key, $val) = each($dat)) {
						if ($key != 0) {  //$dat[0] = label 
							SetType($val,"double");
							if ($val > $maxy) { 
								$maxy = $val ;
							} 
							if ($val < $miny) { 
								$miny = (double) $val ;
							} 
						}
						$tmp++;
					}
				break;
				case "linear-linear":  //X-Y data is passed in as $data[] = (title,x,y,y2,y3,...) which you can use for multi-dimentional plots.

					while (list($key, $val) = each($dat)) {
						if ($key == 1) {  //$dat[0] = label 
							SetType($val,"double");
							if ($val > $maxx) { 
								$maxx = $val;
							} elseif ($val < $min) { 
								$minx = $val;
							}
						} elseif ($key > 1) { 
							SetType($val,"double");
							if ($val > $maxy) { 
								$maxy = $val ;
							} elseif ($val < $miny) { 
								$miny = $val ;
							} 
						}
						$tmp++;
					}
				break;
				case "linear-linear-error":  //Assume 2-D for now, can go higher
				//Regular X-Y data is passed in as $data[] = (title,x,y,error+,error-,y2,error2+,error2-) 

					while (list($key, $val) = each($dat)) {
						if ($key == 1) {  //$dat[0] = label 
							SetType($val,"double");
							if ($val > $maxx) { 
								$maxx = $val;
							} elseif ($val < $min) { 
								$minx = $val;
							}
						} elseif ($key%3 == 2) { 
							SetType($val,"double");
							if ($val > $maxy) { 
								$maxy = $val ;
							} elseif ($val < $miny) { 
								$miny = $val ;
							} 
						} elseif ($key%3 == 0) { 
							SetType($val,"double");
							if ($val > $maxe_plus) { 
								$maxe = $val ;
							} 
						} elseif ($key%3 == 1) { 
							SetType($val,"double");
							if ($val > $maxe_minus) { 
								$mine = $val ;
							} 
						}
						$tmp++;
					}
					$maxy = $maxy + $maxe_plus;  
					$miny = $miny - $maxe_minus; //assume error bars are always > 0
					
				break;
				default:
					echo "ERROR: unknown chart type";
				break;
			} 
			if ($tmp > $max_records_per_group) { 
				$max_records_per_group = $tmp;
			}
		}


		$this->min_x = $minx;
		$this->max_x = $maxx;
		$this->min_y = $miny;
		$this->max_y = $maxy;


		if ($max_records_per_group > 1) { 
			$this->records_per_group = $max_records_per_group - 1;  
		} else { 
			$this->records_per_group = 1;
		}


		//$this->data_count = $total_records ;
	}

	function SetMargins() { 
		/////////////////////////////////////////////////////////////////
		// When the image is first created - set the margins 
		// to be the standard viewport.
		// The standard viewport is the full area of the view surface (or panel),
		// less a margin of 4 character heights all round for labelling.
		// It thus depends on the current character size, set by SetCharacterHeight().
		/////////////////////////////////////////////////////////////////

//echo "SMargins:<br>";
		if ($this->use_ttf == 1) {
			$title_size = $this->TTFBBoxSize($this->title_ttffont_size, $this->title_angle, $this->title_ttffont, "X"); //An array
			$this->y_top_margin = ($title_size[1] * 4);
			$this->y_bot_margin = $this->x_label_height ;
			$this->x_left_margin = $this->y_label_width * 2 + $this->tick_length;
			$this->x_right_margin = 33.0; /* distance between right and end of x axis in pixels */
		} else { 
			$title_size = array($this->title_font_width * strlen($this->plot_title),$this->title_font_height);
			//$this->y_top_margin = ($title_size[1] * 4);
			$this->y_top_margin = $title_size[1] * 4; 
			$this->y_bot_margin = 66.0; /* Must be integer */
			$this->x_left_margin = 77.0; /* distance between left and start of x axis in pixels */
			$this->x_right_margin = 33.0; /* distance between right and end of x axis in pixels */
		}

//echo "$this->x_label_height<br>";
//echo "$this->x_left_margin<br>";
//exit;
		$this->x_tot_margin = $this->x_left_margin + $this->x_right_margin;
		$this->y_tot_margin = $this->y_left_margin + $this->y_right_margin;

		if ($this->plot_max_x && $this->plot_max_y && $this->plot_area_width ) { //If data has already been analysed then set translation
			$this->SetTranslation();
		}
	}
	
/*
	function SetNewPlotAreaPixels($x1,$y1,$x2,$y2) {
		//Like in GD 0,0 is upper left set via pixel Coordinates
		$this->plot_area = array($x1,$y1,$x2,$y2);
		$this->plot_area_width = $this->plot_area[2] - $this->plot_area[0];
		$this->plot_area_height = $this->plot_area[3] - $this->plot_area[1];
		if ($this->plot_max_x) { 
			$this->SetTranslation();
		} 
		return true;
	}
*/

	function SetPlotAreaPixels($x1,$y1,$x2,$y2) {
		//Like in GD 0,0 is upper left
		if (!$this->x_tot_margin) { 
			//echo "SPAP<br>";
			$this->SetMargins();
		}  
		if ($x2 && $y2) { 
			$this->plot_area = array($x1,$y1,$x2,$y2);
		} else { 
			$this->plot_area = array($this->x_left_margin, $this->y_top_margin, 
								$this->image_width - $this->x_right_margin, 
								$this->image_height - $this->y_bot_margin
							);
		}
		$this->plot_area_width = $this->plot_area[2] - $this->plot_area[0];
		$this->plot_area_height = $this->plot_area[3] - $this->plot_area[1];

//echo "SPAP: $this->x_left_margin, $this->y_top_margin,$this->y_bot_margin,$this->x_right_margin<br>";
//echo "SPAP: $this->plot_area[0], $this->plot_area[1],$this->plot_area[2],$this->plot_area[3]<br>";
//exit;

		return true;

	}

	function SetPlotAreaWorld($xmin,$ymin,$xmax,$ymax) {
		if (($xmin == "")  && ($xmax == "")) { 
			//For automatic setting of data we need $this->max_x
				if (!$this->max_y) { 
					$this->FindDataLimits() ;
				}
				if ($this->data_type == "text-linear") { //labels for text-linear is done at data drawing time for speed.
					$xmax = $this->max_x + 1 ;  //valid for BAR CHART TYPE GRAPHS ONLY
					$xmin = 0 ; 				//valid for BAR CHART TYPE GRAPHS ONLY
				} else { 
					$xmax = $this->max_x * 1.02;
					$xmin = $this->min_x;
				}

				$ymax = ceil($this->max_y * 1.2);
				if ($this->min_y < 0) { 
					$ymin = floor($this->min_y * 1.2);
				} else { 
					$ymin = 0;
				}
		}


		$this->plot_min_x = $xmin;
		$this->plot_max_x = $xmax;

		if ($ymin == $ymax) { 
			$ymax += 1;
		}
		$this->plot_min_y = $ymin;
		$this->plot_max_y = $ymax;

//echo "$xmin, $xmax, $ymin, $ymax<br>";
//echo "$this->plot_min_x, $this->plot_max_x, $this->plot_min_y, $this->plot_max_y";
//exit;
		if ($ymax <= $ymin) { 
			$this->DrawError("Error in Data - max not gt min");
		}

//Set the boundaries of the box for plotting in world coord
//		if (!$this->x_tot_margin) { //We need to know the margins before we can calculate scale
//			$this->SetMargins();
//		}  
		//For this we have to reset the scale
		if ($this->plot_area_width) { 
			$this->SetTranslation();
		}

		return true;

	}


	function DrawError($error_message) {
		$ypos = $this->image_height/2;
		if ($this->use_ttf == 1) { 
			ImageTTFText($this->img, $this->small_ttffont_size, 0, $xpos, $ypos, ImageColorAllocate($this->img,0,0,0), $this->axis_ttffont, $error_message);
		} else { 
			ImageString($this->img, $this->small_font,1,$ypos,$error_message, ImageColorAllocate($this->img,0,0,0));
		}
		return true;
	}

	function TTFBBoxSize($size, $angle, $font, $string) {

		//Assume angle < 90
		$arr = ImageTTFBBox($size, 0, $font, $string);
		$flat_width  = $arr[0] - $arr[2];
		$flat_height = abs($arr[3] - $arr[5]);

			// for 90deg:
			//	$height = $arr[5] - $arr[7];
			//	$width = $arr[2] - $arr[4];

		$width  = ceil(abs($flat_width*cos($angle) + $flat_height*sin($angle))); //Must be integer
		$height = ceil(abs($flat_width*sin($angle) + $flat_height*cos($angle))); //Must be integer

		return array($width, $height);
	}

	function SetXLabelHeight() {
   
		if ($this->use_ttf == 1) { 
			//Space for the X Label
			$size = $this->TTFBBoxSize($this->axis_ttffont_size, 0, $this->axis_ttffont, $this->x_label_txt);
			$tmp = $size[1];

			//$string = Str_Repeat("m", $this->x_datalabel_maxlength);
			while ($i < $this->x_datalabel_maxlength) {
			  $string .= "w";
			  $i++;
			}

			//Space for the axis labels
			$size = $this->TTFBBoxSize($this->axis_ttffont_size, $this->x_datalabel_angle, $this->axis_ttffont, $string);
			
			$this->x_label_height = 2*$tmp + $size[1];
//echo "$tmp, $size[1]<br>";
//exit;

		} else { 
			$this->x_label_height = 4 * $this->small_font_height;
		}

//echo "SXLH<br>";
		$this->SetMargins();

		return true;
	}

	function SetYLabelWidth() {
		//$ylab = sprintf("%6.1f %s",$i,$si_units[0]);  //use for PHP2 compatibility
		//the "." is for space. It isn't actually printed
		$ylab = number_format($this->max_y, $this->precision, ".", ",") . $this->si_units . ".";

		if ($this->use_ttf == 1) { 
			$size = $this->TTFBBoxSize($this->axis_ttffont_size, 0, $this->axis_ttffont, $ylab);
		} else { 
			$size[0] = StrLen($ylab) * $this->small_font_width * .75;
		}

		$this->y_label_width = $size[0] * 2;
//echo "SYLW: $this->y_label_width<br>";
//exit;

		$this->SetMargins();
		return true;
	}

	function SetEqualXCoord() {
		//for plots that have equally spaced x variables and multiple bars per x-point.

		$space = ($this->plot_area[2] - $this->plot_area[0]) / ($this->number_x_points * 4);
		$group_width = $space * 3;
		$bar_width = $group_width / $this->records_per_group;
  //I think that eventually this space variable will be replaced by just graphing x. 
		$this->data_group_space = $space;
		$this->record_bar_width = $bar_width;
		return true;
	}

	function SetErrorBarSize($which_ebs) {
		//in pixels
		$this->error_bar_size = $which_ebs;
		return true;
	}

	function SetErrorBarShape($which_ebs) {
		//in pixels
		$this->error_bar_shape = $which_ebs;
		return true;
	}

	function SetPointShape($which_pt) {
		//in pixels
		$this->point_shape = $which_pt;
		return true;
	}

	function SetPointSize($which_ps) {
		//in pixels
		SetType($which_ps,"integer");
		$this->point_size = $which_ps;

		if ($this->point_shape == "diamond" or $this->point_shape == "triangle") {
			if ($this->point_size % 2 != 0) {
				$this->point_size++;
			}
		}
		return true;
	}


	function SetDataType($which_dt) { 
		$this->data_type = $which_dt; //text-linear, linear-linear, linear-linear-error
		return true;
	}

	function SetDataValues($which_dv) { 
		$this->data_values = $which_dv;
//echo $this->data_values
		return true;
	}

	function SetDataColors($which_data,$which_border) {
		//Set the data to be displayed in a particular color
		if ($this->img == "") { 
			$this->InitImage();
		}
		if (!$which_data) { 
			$which_data = array("blue","bisque",array(0,176,0));
			$which_border = array("black");
		}

		$this->data_color = $which_data;  //an array
		$this->border_color = $which_border;  //an array

		//foreach($this->data_color as $col) {
		reset($this->data_color);  //data_color can be an array of colors, one for each thing plotted
		while (list(, $col) = each($this->data_color)) {
			list($r, $g, $b) = SetColor($col);
			$this->col_data_color[] = ImageColorAllocate($this->img, $r, $g, $b);
		}

		// border_color
		//If we are also going to put a border on the data (bars, dots, area, ...)
		//	then lets also set a border color as well.
		//foreach($this->data_border_color as $col) {
		reset($this->data_border_color);
		while (list(, $col) = each($this->data_border_color)) {
			list($r, $g, $b) = SetColor($col);
			$this->col_data_border_color[] = ImageColorAllocate($this->img, $r, $g, $b);
		}

		return true;

	}

	function DrawPlotBorder() { 
		//ImageRectangle($this->img, $this->xtr($this->plot_min_x),$this->ytr($this->plot_min_y),
		//	$this->xtr($this->plot_max_x),$this->ytr($this->plot_max_y),$this->grid_color);
		ImageRectangle($this->img, $this->plot_area[0],$this->ytr($this->plot_min_y),
			$this->plot_area[2],$this->ytr($this->plot_max_y),$this->grid_color);
		$this->DrawVerticalTicks();
		$this->DrawXAxis();
		return true;
	}


	function SetHorizTickIncrement($which_ti) { 
		//Use either this or NumVertTicks to set where to place x tick marks
		if ($which_ti) { 
			$this->horiz_tick_increment = $which_ti;  //world coordinates
		} else { 
			if (!$this->max_x) { 
				$this->FindDataLimits();  //Get maxima and minima for scaling
			}
			//$this->horiz_tick_increment = ( ceil($this->max_x * 1.2) - floor($this->min_x * 1.2) )/10;
			$this->horiz_tick_increment =  ($this->plot_max_x  - $this->plot_min_x  )/10;
		}
		$this->num_horiz_ticks = ""; //either use num_vert_ticks or vert_tick_increment, not both
		return true;
	}

	function SetVertTickIncrement($which_ti) { 
		//Use either this or NumVertTicks to set where to place y tick marks
		if ($which_ti) { 
			$this->vert_tick_increment = $which_ti;  //world coordinates
		} else { 
			if (!$this->max_y) { 
				$this->FindDataLimits();  //Get maxima and minima for scaling
			}
			//$this->vert_tick_increment = ( ceil($this->max_y * 1.2) - floor($this->min_y * 1.2) )/10;
			$this->vert_tick_increment =  ($this->plot_max_y  - $this->plot_min_y  )/10;
		}
		$this->num_vert_ticks = ""; //either use num_vert_ticks or vert_tick_increment, not both
		return true;
	}

	function SetNumVertTicks($which_nt) { 
		$this->num_vert_ticks = $which_nt;
		$this->vert_tick_increment = "";  //either use num_vert_ticks or vert_tick_increment, not both
		return true;
	}
	function SetTickLength($which_tl) { 
		$this->tick_length = $which_tl;
		return true;
	}

	function DrawXAxis() { 
		//Draw X Axis at Y=0
		ImageLine($this->img,(-$this->tick_length+$this->plot_area[0]),
				$this->ytr(0),$this->xtr(1),$this->ytr(0),$this->tick_color);
		ImageLine($this->img,$this->plot_area[0]+1,$this->ytr(0),
				$this->xtr($this->plot_max_x)-1,$this->ytr(0),$this->tick_color);
		ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
				$this->ytr(0),$this->xtr($this->plot_max_x-1),$this->ytr(0),$this->grid_color);

		//Y Axis Label
		$ylab = number_format(0,$this->precision,".",",") . "$this->si_units";
		ImageString($this->img, $this->small_font,($this->plot_area[0] - $this->y_label_width - $this->tick_length/2),
			( -($this->small_font_height/2.0) + $this->ytr(0)),$ylab, $this->text_color);

		//X Ticks and Labels
		if ($this->data_type != "text-linear") { //labels for text-linear is done at data drawing time for speed.
			$this->DrawHorizontalTicks();	
		}
		return true;
	}

	function DrawHorizontalTicks() { 
		//Ticks and lables are drawn on the left border of PlotArea.
		//Left Bottom
		ImageLine($this->img,$this->plot_area[0],
				$this->plot_area[3]+$this->tick_length,
				$this->plot_area[0],$this->plot_area[3],$this->tick_color);
		if ($this->x_grid_label_type == "title") { 
			$xlab = $this->data_values[0][0];
		} else { 
			$xlab = number_format($this->plot_min_x,$this->precision,".",",") . "$this->si_units";
		}
			ImageString($this->img, $this->small_font,($this->plot_area[0] - $this->small_font_width*strlen($xlab)/2 ),
			( $this->small_font_height + $this->plot_area[3]),$xlab, $this->text_color);

		//Top
	
		if ($this->horiz_tick_increment) { 
			$delta_x = $this->horiz_tick_increment;
		} elseif ($this->num_horiz_ticks) { 
			$delta_x = ($this->plot_max_x - $this->plot_min_x) / $this->num_horiz_ticks;
		} else { 
			$delta_x =($this->plot_max_x - $this->plot_min_x) / 10 ; 
		}

		$i = 0;
		$x_tmp = $this->plot_min_x;
		SetType($x_tmp,"double");

		while ($x_tmp <= $this->plot_max_x){
			//$xlab = sprintf("%6.1f %s",$min_x,$si_units[0]);  //PHP2 past compatibility
			if ($this->x_grid_label_type == "title") { 
				$xlab = $this->data_values[$x_tmp][0];
			} else { 
				$xlab = number_format($x_tmp,$this->precision,".",",") . "$this->si_units";
			}

			$x_pixels = $this->xtr($x_tmp);
	
			//Bottom Tick
			ImageLine($this->img,$x_pixels,$this->plot_area[3] + $this->tick_length,
				$x_pixels,$this->plot_area[3], $this->tick_color);
			//Top Tick
			//ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
			//	$y_pixels,$this->xtr($this->plot_max_x)-1,$y_pixels,$this->tick_color);
	
			if ($this->draw_x_grid == 1) { 
				ImageLine($this->img,$x_pixels,$this->plot_area[1] + $this->tick_length,
					$x_pixels,$this_plot_area[3], $this->light_grid_color);
			}
			ImageString($this->img, $this->small_font,($x_pixels - $this->small_font_width*strlen($xlab)/2) ,
				( $this->small_font_height + $this->plot_area[3]),$xlab, $this->text_color);
			$i++;
			$x_tmp += $delta_x;
		}

	}

	function DrawVerticalTicks() { 
		//Ticks and lables are drawn on the left border of PlotArea.
		//Left Bottom
		//ImageLine($this->img,(-$this->tick_length+$this->xtr($this->plot_min_x)),
		//		$this->ytr($this->plot_min_y),$this->xtr(1),$this->ytr($this->plot_min_y),$this->tick_color);
		ImageLine($this->img,(-$this->tick_length+$this->plot_area[0]),
				$this->plot_area[1],$this->xtr(1),$this->plot_area[1],$this->tick_color);

		$ylab = number_format($this->plot_min_y,$this->precision,".",",") . "$this->si_units";
		ImageString($this->img, $this->small_font,($this->plot_area[0] - $this->y_label_width - $this->tick_length/2),
			( -($this->small_font_height/2.0) + $this->ytr($this->plot_min_y)),$ylab, $this->text_color);

		//Right Bottom
		ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
				$this->ytr($this->plot_min_y),$this->xtr($this->plot_max_x),
				$this->ytr($this->plot_min_y),$this->tick_color);

		//Bottom
		ImageLine($this->img,$this->xtr($this->plot_min_x)+1,$this->ytr($this->plot_min_y),
				$this->xtr($this->plot_max_x),$this->ytr($this->plot_min_y),$this->light_grid_color);

		//Left Top
		//ImageLine($this->img,(-$this->tick_length+$this->xtr($this->plot_min_x)),
		//		$this->ytr($this->plot_max_y),$this->xtr(1),$this->ytr($this->plot_max_y),$this->tick_color);
		//$ylab = number_format($this->plot_max_y,$this->precision,".",",") . "$this->si_units";
		//ImageString($this->img, $this->small_font,($this->plot_area[0] - $this->y_label_width - $this->tick_length/2),
		//	( -($this->small_font_height/2.0) + $this->ytr($this->plot_max_y)),$ylab, $this->text_color);

		//Right Top
		//ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
		//		$this->ytr($this->plot_max_y),$this->xtr($this->plot_max_x-1),$this->ytr($this->plot_max_y),$this->tick_color);

		//Top
		//ImageLine($this->img,$this->xtr($this->plot_min_x)+1,$this->ytr($this->plot_max_y),
		//		$this->xtr($this->plot_max_x)-1,$this->ytr($this->plot_max_y),$this->light_grid_color);
		ImageLine($this->img,$this->plot_area[0]+1,$this->ytr($this->plot_max_y),
				$this->xtr($this->plot_max_x)-1,$this->ytr($this->plot_max_y),$this->light_grid_color);
	
		/* maxy is always > miny so delta_y is always positive */
		if ($this->vert_tick_increment) { 
			$delta_y = $this->vert_tick_increment;
		} elseif ($this->num_vert_ticks) { 
			$delta_y = ($this->plot_max_y - $this->plot_min_y) / $this->num_vert_ticks;
		} else { 
			$delta_y =($this->plot_max_y - $this->plot_min_y) / 10 ; 
		}

		$i = 0;
		$y_tmp = $this->plot_min_y;
		SetType($y_tmp,"double");

		while ($y_tmp <= $this->plot_max_y){
			//$ylab = sprintf("%6.1f %s",$min_y,$si_units[0]);  //PHP2 past compatibility
			$ylab = number_format($y_tmp,$this->precision,".",",") . "$this->si_units";
			$y_pixels = $this->ytr($y_tmp);
	
			//ImageLine($this->img,(-$this->tick_length+$this->xtr($this->plot_min_x)),
			ImageLine($this->img,(-$this->tick_length+$this->plot_area[0]),
				$y_pixels,$this->plot_area[0],
				$y_pixels, $this->tick_color);
			ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
				$y_pixels,$this->plot_area[2],
				$y_pixels,$this->tick_color);
	
			// gdStyled only supported with GD v3 
			//ImageLine($this->img,$this->xtr($this->plot_min_x),$this->ytr($y_tmp),$this->xtr($this->plot_max_x),$this->ytr($y_tmp),gdStyled);
			if ($this->draw_y_grid == 1) { 
				ImageLine($this->img,$this->xtr($this->plot_min_x),$y_pixels,
					$this->xtr($this->plot_max_x),$y_pixels,$this->light_grid_color);
			}
			ImageString($this->img, $this->small_font,($this->plot_area[0] - $this->y_label_width - $this->tick_length/2),
				( -($this->small_font_height/2.0) + $y_pixels),$ylab, $this->text_color);
			$i++;
			$y_tmp += $delta_y;
		}

//echo "$this->plot_min_x, $this->plot_max_x, $this->plot_min_y, $this->plot_max_y";
//exit;
		return true;
		
	}

	function SetTranslation() {

		$this->xscale = ($this->plot_area_width)/($this->plot_max_x - $this->plot_min_x);
		$this->yscale = ($this->plot_area_height)/($this->plot_max_y - $this->plot_min_y); 
//echo "$this->xscale = ($this->plot_area_width)/($this->plot_max_x - $this->plot_min_x)<br>";
//echo "$this->yscale = ($this->plot_area_height)/($this->plot_max_y - $this->plot_min_y)<br>"; 
		//$this->xscale = ($this->image_width - $this->x_tot_margin)/($this->plot_max_x - $this->plot_min_x);
		//$this->yscale = ($this->image_height - $this->y_tot_margin)/($this->plot_max_y - $this->plot_min_y); 

		$this->plot_origin_x = $this->plot_area[0] - ($this->xscale * $this->plot_min_x ); //- becuase GD defines x=0 at left
		$this->plot_origin_y = $this->plot_area[3] + ($this->yscale * $this->plot_min_y);  //+ becuase GD defines y=0 at top
//echo "DEBUG ST: $this->plot_origin_y , $this->plot_area_height , $this->yscale<br>";
//echo "DEBUG ST: $this->plot_origin_x , {$this->plot_area[0]} , $this->xscale<br>";
//exit;
	}

	function xtr($x_world) { 
	//Translate world coordinates into pixel coordinates
	//The pixel coordinates are those of the ENTIRE image, not just the plot_area
		//$x_pixels =  $this->x_left_margin + ($this->image_width - $this->x_tot_margin)*(($x_world - $this->plot_min_x) / ($this->plot_max_x - $this->plot_min_x)) ; 
		//which with a little bit of math reduces to ...
		$x_pixels =  $this->plot_origin_x + $x_world * $this->xscale ; 
		return($x_pixels);
	}

	function ytr($y_world) {
		// translate y world coord into pixel coord 
		//$yscale = ($image_height - $y_tot_margin)/($maxy-$miny); /* Maximum Height in Screen Units */
		//$y_pixels = ($this->plot_max_y - $y_world) * $this->yscale + $this->y_top_margin ;
		$y_pixels =  $this->plot_origin_y - $y_world * $this->yscale ;  //minus because GD defines y=0 at top. doh!
//echo "DEBUG: $this->plot_origin_y , $y_world , $this->yscale<br>";
//exit;
		return ($y_pixels);
	}


	function DrawDataLabel($lab,$x_world,$y_world) { 
		//Data comes in in WORLD coordinates
		//Draw data label near actual data point
			if ($this->use_ttf) { 
				$lab_size = $this->TTFBBoxSize($this->axis_ttffont_size, $this->x_datalabel_angle, $this->axis_ttffont, $lab); //An array
				$y = $this->ytr($y_world) - $lab_size[1] ;  //in pixels
				$x = $this->xtr($x_world) - $lab_size[0]/2;
				ImageTTFText($this->img, $this->axis_ttffont_size, $this->x_datalabel_angle, $x, $y, $this->text_color, $this->axis_ttffont, $lab);
			} else { 
				$lab_size = array($this->small_font_width*StrLen($lab), $this->small_font_height*3);
				$y = $this->ytr($y_world) - $this->small_font_height; //in pixels
				$x = $this->ytr($x_world) - ($this->small_font_width*StrLen($lab))/2;
				ImageString($this->img, $this->small_font,$x, $y ,$lab, $this->axis_font);
			}

	} 

	function DrawXDataLabel($xlab,$xpos) { 
		//xpos comes in in PIXELS not in world coordinates. 
		//Draw an x data label centered at xlab
			if ($this->use_ttf) { 
				$xlab_size = $this->TTFBBoxSize($this->axis_ttffont_size, $this->x_datalabel_angle, $this->axis_ttffont, $xlab); //An array
				$y = $this->plot_area[3] + $xlab_size[1] + 2;  //in pixels
				$x = $xpos - $xlab_size[0]/2;
				ImageTTFText($this->img, $this->axis_ttffont_size, $this->x_datalabel_angle, $x, $y, $this->text_color, $this->axis_ttffont, $xlab);
			} else { 
				$xlab_size = array($this->small_font_width*StrLen($xlab), $this->small_font_height*3);
				$y = $this->plot_area[3] + $this->small_font_height*1; //in pixels
				$x = $xpos - ($this->small_font_width*StrLen($xlab))/2;
				ImageString($this->img, $this->small_font,$x, $y ,$xlab, $this->axis_font);
			}

	} 

	function DrawPieChart() { 
		//$pi = "3.14159265358979323846";
		$xpos = $this->plot_area[0] + $this->plot_area_width/2;
		$ypos = $this->plot_area[1] + $this->plot_area_height/2;
		$diameter = (min($this->plot_area_width, $this->plot_area_height)) ;
		$radius = $diameter/2;
		
		ImageArc($this->img, $xpos, $ypos, $diameter, $diameter, 0, 360, $this->grid_color);

		//foreach ...
		reset($this->data_values);
		while (list(, $row) = each($this->data_values)) {
			//Get sum of each type
			$color_index = 0;
			$i = 0;
			//foreach ($row as $v) {
			while (list($k, $v) = each($row)) {
				if ($k != 0) {
					$sumarr[$i] += $v;
				}
			$i++;
			}
		}

		$total = 0;
		$i=1;  //i=0 is for the title, not counted in sum.
		reset($sumarr);
		while (list(, $row) = each($sumarr)) { 
			$sumarr[$i] = abs($sumarr[$i]); // NOTE! You have to have the sum > 0 to make pie charts
			$total += $sumarr[$i];
			$i++;
		}

//echo "$i, $sumarr[0], $sumarr[1], $sumarr[2], $sumarr[3], $sumarr[4], $total<br>";
		$color_index = 0;
		$start_angle = 0; 

		//foreach($sumarr as $val) 
		reset($sumarr);
		while (list(, $val) = each($sumarr)) {
			if ($color_index >= count($this->data_color)) $color_index=0;  //data_color = array
			$label_txt = number_format(($val / $total * 100), $this->precision, ".", ",") . "%";
			$val = 360 * ($val / $total);

			$end_angle += $val;
			$mid_angle = $end_angle - ($val / 2);

			$slicecol = $this->col_data_color[$color_index];

//Need this again for FillToBorder
			ImageArc($this->img, $xpos, $ypos, $diameter, $diameter, 0, 360, $this->grid_color);

			$out_x = $radius * cos(deg2rad($end_angle));
			$out_y = - $radius * sin(deg2rad($end_angle));

			$halfradius = $radius / 2;
			$label_x = $xpos + ($halfradius * cos(deg2rad($mid_angle)));
			$label_y = $ypos + (- $halfradius * sin(deg2rad($mid_angle)));

			$out_x = $xpos + $out_x;
			$out_y = $ypos + $out_y;

			ImageLine($this->img, $xpos, $ypos, $out_x, $out_y, $this->grid_color);
			//ImageLine($this->img, $xpos, $ypos, $label_x, $label_y, $this->grid_color);
			ImageFillToBorder($this->img, $label_x, $label_y, $this->grid_color, $slicecol);

			if ($this->use_ttf) { 
				ImageTTFText($this->img, $this->axis_ttffont_size, 0, $label_x, $label_y, $this->grid_color, $this->axis_ttffont, $label_txt);
			} else { 
				ImageString($this->img, $this->small_font, $label_x, $label_y, $label_txt, $this->grid_color);
			}

			$start_angle = $val;

			$color_index++;
		}

	}

	function DrawLinesError() { 
		//Draw Lines with Error Bars - data comes in as array("title",x,y,error+,error-,y2,error2+,error2-,...);
		$start_lines = 0;

		reset($this->data_values);
		while (list(, $row) = each($this->data_values)) {
			$color_index = 0;
			$i = 0;

			while (list($key, $val) = each($row)) {
//echo "$key, $i, $val<br>";
				if ($key == 0) {  
					$lab = $val;
				} elseif ($key == 1) {  
					$x_now = $val;
					$x_now_pixels = $this->xtr($x_now); //Use a bit more memory to save 2N operations.
				} elseif ($key%3 == 2) { 
					$y_now = $val;
					$y_now_pixels = $this->ytr($y_now);
	
					//Draw Data Label
					if ( $this->draw_data_labels == 1) {
						$this->DrawDataLabel($lab,$x_now,$y_now);
					}
	
					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];
	
//echo "start = $start_lines<br>";
					if ($start_lines == 1) { 
						for ($width = 0; $width < $this->line_width; $width++) {
							ImageLine($this->img, $x_now_pixels, $y_now_pixels + $width, 
								$lastx[$i], $lasty[$i] + $width, $barcol);
						}
					} 
	
					$lastx[$i] = $x_now_pixels;
					$lasty[$i] = $y_now_pixels;
					$color_index++;
					$i++;
					$start_lines = 1;
				} elseif ($key%3 == 0) { 
					$this->DrawYErrorBar($x_now,$y_now,$val,$this->error_bar_shape,$barcol);
				} elseif ($key%3 == 1) { 
					$mine = $val ;
					$this->DrawYErrorBar($x_now,$y_now,-$val,$this->error_bar_shape,$barcol);
				}
			}
		}
	} 

	function DrawDotsError() { 
		//Draw Dots - data comes in as array("title",x,y,error+,error-,y2,error2+,error2-,...);
		reset($this->data_values);
		while (list(, $row) = each($this->data_values)) {
			$color_index = 0;
			//foreach ($row as $v) {
			while (list($key, $val) = each($row)) {
				if ($key == 0) {  
				} elseif ($key == 1) {  
					$xpos = $val;
//$xtmp = $val;
//echo "$val, $xpos, $ypos, <br>";
				} elseif ($key%3 == 2) { 
					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];
					$ypos = $val;
//$ytmp = $val;
//echo "two: $val, $xpos, $ypos, <br>";

					$color_index++;
					$this->DrawDot($xpos,$ypos,$this->point_shape,$barcol);
				} elseif ($key%3 == 0) { 
					$this->DrawYErrorBar($xpos,$ypos,$val,$this->error_bar_shape,$barcol);
//echo "three: $val, $xpos, $ypos, $xtmp, $ytmp<br>";
				} elseif ($key%3 == 1) { 
					$mine = $val ;
					$this->DrawYErrorBar($xpos,$ypos,-$val,$this->error_bar_shape,$barcol);
				}
			}
		}

	}

	function DrawDots() { 
		//Draw Dots - data comes in as array("title",x,y1,y2,y3,...);
		reset($this->data_values);
		while (list(, $row) = each($this->data_values)) {
			$color_index = 0;
			//foreach ($row as $v) {
			while (list($k, $v) = each($row)) {
				if ($k == 0) {
				} elseif ($k == 1) { 
					$xpos = $v;
				} else { 
					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];

					$this->DrawDot($xpos,$v,$this->point_shape,$barcol);
					$color_index++;
				}
			}
		}

	}

	function DrawDotSeries() { 
		//foreach ($this->data_values as $row) {
		reset($this->data_values);
		while (list($j, $row) = each($this->data_values)) {
			$color_index = 0;
			//foreach ($row as $v) {
			while (list($k, $v) = each($row)) {
				if ($k != 0) {
					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];

					$this->DrawDot($j+.5,$v,$this->point_shape,$barcol);
					$color_index++;
				}
			}
		}

	}

	function DrawYErrorBar($x_world,$y_world,$error_height,$error_bar_type,$color) { 
		$x1 = $this->xtr($x_world);
		$y1 = $this->ytr($y_world);
		$y2 = $this->ytr($y_world+$error_height) ;

		for ($width = 0; $width < $this->line_width; $width++) {
			ImageLine($this->img, $x1+$width, $y1 , $x1+$width, $y2, $color);
			ImageLine($this->img, $x1-$width, $y1 , $x1-$width, $y2, $color);
		}
		switch ($error_bar_type) {
			case "line":
				break;
			case "tee":
				ImageLine($this->img, $x1-$this->error_bar_size, $y2, $x1+$this->error_bar_size, $y2, $color);
				break;
			default:
				ImageLine($this->img, $x1-$this->error_bar_size, $y2, $x1+$this->error_bar_size, $y2, $color);
				break;
		}
		return true;
	}

	function DrawDot($x_world,$y_world,$dot_type,$color) { 
		$half_point = $this->point_size / 2;
		$x1 = $this->xtr($x_world) - $half_point;
		$x2 = $this->xtr($x_world) + $half_point;
		$y1 = $this->ytr($y_world) - $half_point;
		$y2 = $this->ytr($y_world) + $half_point;

		switch ($dot_type) {
			case "halfline":
				ImageFilledRectangle($this->img, $x1, $this->ytr($y_world), $this->xtr($x_world), $this->ytr($y_world), $color);
				break;
			case "line":
				ImageFilledRectangle($this->img, $x1, $this->ytr($y_world), $x2, $this->ytr($y_world), $color);
				break;
			case "rect":
				ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $color);
				break;
			case "circle":
				ImageArc($this->img, $x1 + $half_point, $y1 + $half_point, $this->point_size, $this->point_size, 0, 360, $color);
				break;
			case "dot":
				ImageArc($this->img, $x1 + $half_point, $y1 + $half_point, $this->point_size, $this->point_size, 0, 360, $color);
				ImageFillToBorder($this->img, $x1 + $half_point, $y1 + $half_point, $color, $color);
				break;
			case "diamond":

				$arrpoints = array(
					$x1,$y1 + $half_point,
					$x1 + $half_point, $y1,
					$x2,$y1 + $half_point,
					$x1 + $half_point, $y2
				);

				ImageFilledPolygon($this->img, $arrpoints, 4, $color);
				break;
			case "triangle":
				$arrpoints = array( $x1, $y1 + $half_point,
					$x2, $y1 + $half_point,
					$x1 + $half_point, $y2
				);
				ImageFilledPolygon($this->img, $arrpoints, 3, $color);
				break;
			default:
				ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $color);
				break;
		}
		return true;
	}

	function SetLineWidth($which_lt) { 
		$this->line_width = $which_lt;
		return true;
	}

	function DrawAreaSeries() { 

		//Set first and last datapoints of area
		$i = 0;
		while ($i < $this->records_per_group) { 
			$posarr[$i][] =  $this->xtr(.5);			//x initial
			$posarr[$i][] =  $this->ytr(0); 	//y initial
			$i++;
		}	

		reset($this->data_values);
		while (list($j, $row) = each($this->data_values)) {
			$color_index = 0;
			//foreach ($row as $v) 
			while (list($k, $v) = each($row)) {
				if ($k == 0) {
					//Draw Data Labels
					$xlab = SubStr($v,0,$this->x_datalabel_maxlength);
					$this->DrawXDataLabel($xlab,$this->xtr($j + .5));
				} else {
					// Create Array of points for later

					$x = round($this->xtr($j + .5 ));
					$y = round($this->ytr($v));
					$posarr[$color_index][] = $x;
					$posarr[$color_index][] = $y;
					$color_index++;
				}
			}
		}

		//Final_points
		for ($i = 0; $i < $this->records_per_group; $i++) {
			$posarr[$i][] =  round($this->xtr($this->max_x + .5));	//x final
			$posarr[$i][] =  $this->ytr(0); 		//y final
	   	}

		$color_index=0;

		//foreach($posarr as $row) {
		reset($posarr);
		while (list(, $row) = each($posarr)) {
			if ($color_index >= count($this->data_color)) $color_index=0;
			$barcol = $this->col_data_color[$color_index];
//echo "$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12], $barcol<br>";
			ImageFilledPolygon($this->img, $row, (count($row)) / 2, $barcol);
			$color_index++;
		}
//exit;

	}

	function DrawLines() { 
		//Data comes in as $data[]=("title",x,y,...);
		$start_lines = 0;

		//foreach ($this->data_values as $row) 
		reset($this->data_values);
		while (list($j, $row) = each($this->data_values)) {

			$color_index = 0;
			$i = 0;
			//foreach ($row as $v) 
			while (list($k, $v) = each($row)) {
				if ($k == 0)  { 
					if ( $this->draw_x_data_labels == 1) {
						$xlab = SubStr($v,0,$this->x_datalabel_maxlength);
						$this->DrawXDataLabel($xlab,$this->xtr($row[1]));
					}
				} elseif ($k == 1) { 
					$x_now = $this->xtr($v);
				} else {
					// Draw Lines

					$y_now = $this->ytr($v);

					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];

					if ($start_lines == 1) { 
						for ($width = 0; $width < $this->line_width; $width++) {
							ImageLine($this->img, $x_now, $y_now + $width, $lastx[$i], $lasty[$i] + $width, $barcol);
							//ImageLine($this->img, $x_now, $y_now + $width, $x2, $lasty[$i] + $width, $barcol);
						}
					} 
					$bordercol = $this->col_bar_border_color[$colbarcount];

					$lastx[$i] = $x_now;
					$lasty[$i] = $y_now;
					$color_index++;
					$i++;
				}
			}
			$start_lines = 1;
		}
	} 

		//Data comes in as $data[]=("title",x,y,e+,e-,y2,e2+,e2-,...);

	function DrawLineSeries() { 
		//Like DrawBars - but use lines. Data comes in as $data[]=("title",y1,y2,y3,...);
		$start_lines = 0;
		$lastx[0] = 0;
		$lasty[0] = 0;


		//foreach ($this->data_values as $row) {
		reset($this->data_values);
		while (list($j, $row) = each($this->data_values)) {

			$color_index = 0;
			$i = 0;
			//foreach ($row as $v) {
			while (list($k, $v) = each($row)) {
				if ($k == 0) {
					//Draw Data Labels
					$xlab = SubStr($v,0,$this->x_datalabel_maxlength);
					$this->DrawXDataLabel($xlab,$this->xtr($j + .5));
				} else { 
					// Draw Lines

					$x1 = $this->xtr($j + .5 );
					$x2 = $this->xtr($j - .5);
					$y1 = $this->ytr($v);

					if ($color_index >= count($this->data_color)) $color_index=0;
					$barcol = $this->col_data_color[$color_index];

					if ($start_lines == 1) { 
						for ($width = 0; $width < $this->line_width; $width++) {
							//ImageLine($this->img, $x1, $y1 + $width, $lastx[$i], $lasty[$i] + $width, $barcol);
							ImageLine($this->img, $x1, $y1 + $width, $x2, $lasty[$i] + $width, $barcol);
						}
					} 

					$bordercol = $this->col_bar_border_color[$colbarcount];

					$lastx[$i] = $x1;
					$lasty[$i] = $y1;

					$color_index++;
					$i++;
				}
			}
			$start_lines = 1;
		}
//exit;
	}

	function DrawBars() {

		$start_pos = $this->plot_area[0] + $this->data_group_space / 2;
		$xadjust = ($this->records_per_group * $this->data_group_space )/2;

		//foreach ($this->data_values as $row) {
		reset($this->data_values);
		while (list(, $row) = each($this->data_values)) {

			$color_index = 0;
			$colbarcount = 0;
			//foreach ($row as $v) {
			while (list($k, $v) = each($row)) {
				if ($k == 0) {
					//Draw Data Labels
					$xlab = SubStr($v,0,$this->x_datalabel_maxlength);
					$this->DrawXDataLabel($xlab,($start_pos + $xadjust));

				} else { 
					// Draw Bars ($v)

					$x1 = $start_pos;
					$x2 = $start_pos + $this->record_bar_width;
					if ($v < 0) { 
						$y1 = $this->ytr(0);
						$y2 = $this->ytr($v);
					} else { 
						$y1 = $this->ytr($v);
						$y2 = $this->ytr(0);
					}

					if ($color_index >= count($this->data_color)) $color_index=0;
					if ($colbarcount >= count($this->bar_border_color)) $colbarcount=0;
					$barcol = $this->col_data_color[$color_index];
					$bordercol = $this->col_bar_border_color[$colbarcount];

					if ($this->shading == 1) {
						//Shading set in SetDefaultColors
						ImageFilledRectangle($this->img, $x1+1, $y1-1, $x2+1, $y2-1, $this->i_light);
						ImageFilledRectangle($this->img, $x1+2, $y1-2, $x2+2, $y2-2, $this->i_light);
					}

					ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $barcol);
					//ImageRectangle($this->img, $x1, $y1, $x2, $y2, $bordercol);
					ImageRectangle($this->img, $x1, $y1, $x2, $y2, $this->text_color);

					$start_pos = $start_pos + $this->record_bar_width;
					$color_index++;
					$colbarcount++;
				}
			}
			$start_pos = $start_pos + $this->data_group_space;
		}
	}

	function DrawGraph() {

		if ( ($this->img) == "") {
			$this->InitImage();
		}
		if (! is_array($this->data_values)) {
			$this->DrawBackground();
			$this->DrawError("No array of data in \$data_values");
		} else {
			if (!$this->data_color) { 
				$this->SetDataColors(array("blue","green","yellow"),array("black"));
			}

			$this->FindDataLimits();  //Get maxima and minima for scaling

			$this->SetXLabelHeight();		//Get data for bottom margin

			$this->SetYLabelWidth();		//Get data for left margin

			if (!$this->plot_area_width) { 
				$this->SetPlotAreaPixels("","","","");		//Set Margins
			}

			if (!$this->plot_max_y) {  //If not set by user call SetPlotAreaWorld, 
				$this->SetPlotAreaWorld("","","","");
			}

			if ($this->data_type == "text-linear") { 
				$this->SetEqualXCoord();
			}

			$this->SetPointSize($this->point_size);

			$this->DrawBackground();
			$this->DrawImageBorder();

			$this->SetTranslation();

			$this->DrawPlotAreaBackground();
//$foo = "$this->max_y, $this->min_y, $new_miny, $new_maxy, $this->x_label_height";
//ImageString($this->img, 4, 20, 20, $foo, $this->text_color);

			switch ($this->plot_type) {
				case "bars":
					$this->DrawPlotBorder();
					$this->DrawLabels();
					$this->DrawBars();
					break;
				case "lines":
					$this->DrawPlotBorder();
					$this->DrawLabels();
					if ( $this->data_type == "text-linear") {
						$this->DrawLineSeries();
					} elseif ( $this->data_type == "linear-linear-error") {
						$this->DrawLinesError();
					} else { 
						$this->DrawLines();
					}
					break;
				case "area":
					$this->DrawPlotBorder();
					$this->DrawLabels();
					$this->DrawAreaSeries();
					break;
				case "linepoints":
					$this->DrawPlotBorder();
					$this->DrawLabels();
					if ( $this->data_type == "text-linear") {
						$this->DrawLineSeries();
						$this->DrawDotSeries();
					} elseif ( $this->data_type == "linear-linear-error") {
						$this->DrawLinesError();
						$this->DrawDotsError();
					} else { 
						$this->DrawLines();
						$this->DrawDots();
					}
					break;
				case "points";
					$this->DrawPlotBorder();
					$this->DrawLabels();
					if ( $this->data_type == "text-linear") {
						$this->DrawDotSeries();
					} elseif ( $this->data_type == "linear-linear-error") {
						$this->DrawDotsError();
					} else {
						$this->DrawDots();
					}
					break;
				case "pie":
					$this->DrawPieChart();
					break;
				default:
					$this->DrawPlotBorder();
					$this->DrawLabels();
					$this->DrawBars();
					break;
			}

//			$this->DrawLegend();

		}
		$this->PrintImage();
	}

 }

// $graph = new PHPlot;

// $graph->DrawGraph();

?>
