<?php

/* $Id$ */

/*
 * Copyright (C) 1998, 1999, 2000, 2001 Afan Ottenheimer.  Released under
 * the GPL and PHP licenses as stated in the the README file which
 * should have been included with this document.
 *
 * Recent (2003) work by Miguel de Benito Delgado <nonick AT 8027 DOT org> 
 *
 * World Coordinates are the XY coordinates relative to the
 * axis origin that can be drawn. Not the device (pixel) coordinates
 * which in GD is relative to the origin at the upper left
 * side of the image.
 */


//PHPLOT Version 4.?.?
//Requires PHP 4 or later 

//error_reporting(E_ALL);

class PHPlot {

    var $is_inline = 0;             // 0 = Sends headers, 1 = sends just raw image data
    var $browser_cache = 0;         // 0 = Sends headers for browser to not cache the image, 
                                    // (i.e. 0 = don't let browser cache image)
                                    // (only if is_inline = 0 also)
    var $session_set = '';          // Do not change
    var $scale_is_set = '';         // Do not change
    var $draw_plot_area_background = 0;

    var $image_width;               // Total Width in Pixels 
    var $image_height;              // Total Height in Pixels
    var $image_border_type = '';    // raised, plain, ''

    var $safe_margin = 5;
    var $x_left_margin;
    var $y_top_margin;
    var $x_right_margin;
    var $y_bot_margin;
    
    var $plot_area = array(5,5,600,400);        // ?? (MBD)
    
    var $x_axis_position = 0;       // Where to draw the X_axis (world coordinates)
    var $y_axis_position = '';      // Leave blank for Y axis at left of plot. (world coord.)
    
    var $xscale_type = 'linear';    // linear, log
    var $yscale_type = 'linear';

//Use for multiple plots per image
    var $print_image = 1;           // Used for multiple charts per image. 

//Fonts
    var $use_ttf  = 0;              // Use TTF fonts (1) or not (0)
    var $ttf_path = '.';           // Default path to look in for TT Fonts. (MBD)
    var $default_ttfont = 'benjamingothic.ttf';
    
    // TTF:
    var $generic_ttfont;
    var $title_ttfont;
    var $legend_ttfont;
    var $x_label_ttfont;
    var $y_label_ttfont;
    var $x_title_ttfont;
    var $y_title_ttfont;
    
    // Fixed:           fonts = 1,2,3,4 or 5
    var $generic_font;
    var $title_font;
    var $legend_font;
    var $x_label_ttfont;
    var $y_label_ttfont;
    var $x_title_font;
    var $y_title_font;
    
    // Font angles:     0 or 90 degrees
    var $title_angle = 0;
    var $x_label_angle = 0;
    var $x_title_angle = 0;                 // This shouldn't be changed!
    var $y_title_angle = 90;                // Nor this.
    
    var $y_margin_width = '';

//Formats
    var $file_format = 'png';
    var $output_file = '';                    // For output to a file instead of stdout

//Plot Colors
    var $shading = 0;
    var $color_array = 2;                   // 1 = include small list, 2 = include large list
                                            // array =  define your own color translation. See rgb.inc.php and SetRGBArray
    var $bg_color;
    var $plot_bg_color;
    var $grid_color;
    var $light_grid_color;
    var $tick_color;
    var $title_color;
    var $label_color;
    var $text_color;
    var $i_light = '';

//Data
    var $data_type = 'text-data';           // text-data, data-data-error, data-data 
    var $plot_type= 'linepoints';           // bars, lines, linepoints, area, points, pie, thinbarline
    var $line_width = 1;
    var $line_style = NULL;                 // array, 'solid' or 'dashed' lines
    
    var $data_color = NULL;                 // array, to be set by the constructor
    var $data_border_color = NULL;          // array, to be set by the constructor

    var $label_scale_position = '.5';       // 1 = top, 0 = bottom
    var $group_frac_width = '.7';           // value from 0 to 1 = width of bar
    var $bar_width_adjust = '1';            // 1 = bars of normal width, must be > 0

    var $point_size = 10;
    var $point_shape = 'diamond';           // rect, circle, diamond, triangle, dot, line, halfline
    var $error_bar_shape = 'tee';           // 'tee' or 'line'
    var $error_bar_size = 5;                // right left size of tee
    var $error_bar_line_width = '';         // If set, then use it, else use $line_width for thickness
    var $error_bar_color = ''; 
    var $data_values;
    var $data_count;
    
    var $plot_border_type = 'full';         // left, none, full
    var $plot_area_width = '';
    var $number_x_points;
    var $plot_min_x = '';                   // Max and min of the plot area
    var $plot_max_x = '';                   // Max and min of the plot area
    var $plot_min_y = '';                   // Max and min of the plot area
    var $plot_max_y = '';                   // Max and min of the plot area
    var $min_y = '';
    var $max_y = '';
    var $max_x = 10;                        // Must not be = 0;
    var $max_t;                             // Maximum number of chars in text fields (calculated from data)
    var $y_precision = '1';
    var $x_precision = '1';
    var $si_units = '';

//Labels
    var $draw_plot_labels = 0;              // Draw Labels next to datapoints
    var $draw_x_data_labels = '';           // Draw X Labels (after datapoint values) [0,1, '' = let program decide]

    var $legend = '';                       // An array with legend titles
    var $legend_x_pos = '';
    var $legend_y_pos = '';

// Titles
    var $title_txt = '';
    
    var $x_title_txt = '';
    var $x_title_pos = 'plotdown';          // plotdown, plotup, both, none
    
    var $y_title_txt = '';
    var $y_title_pos = 'plotleft';          // plotleft, plotright, both, none
    
//DataAxis Labels (on each axis)
    var $x_tick_label_type = 'data';        // data, title, time, other, none
    var $x_tick_label_pos = 'plotdown';     // plotdown, plotup, both, none

    var $y_tick_label_type = 'data';        // data, none, time, other
    var $y_tick_label_pos = 'plotleft';     // plotleft, plotright, yaxis, both, none

    var $x_time_format = "%H:%m:%s";        // See http://www.php.net/manual/html/function.strftime.html
    var $y_time_format = "%H:%m:%s";        // SetYTimeFormat() too... (MBD)
    
    var $x_tick_label_height;
    var $y_tick_label_width;

//Tick Formatting
    var $htick_length = 5;                  // pixels: tick length for upper/lower axis
    var $vtick_length = 5;                  // pixels: tick length for left/right axis
    
    var $num_vert_ticks = '';
    var $vert_tick_increment='';            // Set num_vert_ticks or vert_tick_increment, not both.
    var $vert_tick_position = 'plotleft';   // plotright, plotleft,both, yaxis, none (MBD)
    
    var $num_horiz_ticks='';
    var $horiz_tick_increment='';           // Set num_horiz_ticks or horiz_tick_increment, not both.
    var $horiz_tick_position = 'plotdown';  // plotdown, plotup, both, none (MBD)
    
    var $skip_top_tick = '0';
    var $skip_bottom_tick = '0';

//Grid Formatting
    var $draw_x_grid = 1;
    var $draw_y_grid = 1;

    var $dashed_grid = 1;                   // 0 = false, 1 true (MBD)


//BEGIN CODE
//////////////////////////////////////////////////////
    //Constructor: Setup Img pointer, Colors and Size of Image, and font sizes
    function PHPlot($which_width=600,$which_height=400,$which_output_file="",$which_input_file="") {
    
        $this->SetRGBArray('2'); 
        $this->background_done = 0; //Set to 1 after background image first drawn

        if ($which_output_file != "")
            $this->SetOutputFile($which_output_file);

        if ($which_input_file != "")
            $this->SetInputFile($which_input_file) ; 
        else {
            $this->image_width = $which_width;
            $this->image_height = $which_height;

            $this->InitImage();
        }

        // For sessions
        if ( ($this->session_set == 1) && ($this->img == "") ) {
            // Do nothing
        } else {
            $this->SetDefaultColors();
        }
        
        $this->SetIndexColors();
    
        
        // Init some font-related variables:
        $this->SetDefaultFonts();
        $this->SetTitle('');
        $this->SetXTitle('');
        $this->SetYTitle('');
       
        //Solid or dashed lines
        $this->line_style = array('solid','solid','dashed','dashed','solid', 'solid','dashed','dashed');

        // Dashed grid?
        // The grid is always drawn before the lines so we should be safe with dashed lines
        // which change the style with imagesetstyle() too...
        if ($this->dashed_grid == 1)
            $this->SetDashedStyle($this->ndx_light_grid_color);
    }

    //Set up the image and colors
    function InitImage() {
        //if ($this->img) { 
        //    ImageDestroy($this->img);
        //}
        $this->img = ImageCreate($this->image_width, $this->image_height);
        return true;
    }

    function SetBrowserCache($which_browser_cache) {  //Submitted by Thiemo Nagel
        $this->browser_cache = $which_browser_cache;
        return true;
    }

    function SetPrintImage($which_pi) {
        $this->print_image = $which_pi;
        return true;
    }

/////////////////////////////////////////////
//////////////                          FONTS
/////////////////////////////////////////////

    /*!
     * Enables use of TrueType fonts in the graph. Font initialisation methods
     * depend on this setting, so when called, SetUseTTF() resets the font
     * settings
     */
    function SetUseTTF($which_ttf) {
        $this->use_ttf = $which_ttf;
        if ($which_ttf)
            $this->SetDefaultFonts();
        return true;
    }

    /*!
     * Sets the directory name to look into for TrueType fonts.
     * TODO: use document root?
     */
    function SetTTFPath($which_path)
    {
        // Maybe someone needs really dynamic config. He'll need this:
        // clearstatcache();
        
        if (is_dir($which_path) && is_readable($which_path)) {
            $this->ttf_path = $which_path;
            return TRUE;
        } else {
            $this->PrintError("SetTTFPath(): $which_path is not a valid path.");
            return FALSE;
        }
    }
    
    /*!
     * Reverts fonts to their defaults
     */
    function SetDefaultFonts() {
    
        // TrueType:
        if ($this->use_ttf) {
            $this->SetTTFPath(".");
            $this->SetFont('generic', $this->default_ttfont, 12);
            $this->SetFont('title', $this->default_ttfont, 14);
            $this->SetFont('legend', $this->default_ttfont, 10);
            $this->SetFont('x_label', $this->default_ttfont, 8);
            $this->SetFont('y_label', $this->default_ttfont, 8);
            $this->SetFont('x_title', $this->default_ttfont, 12);
            $this->SetFont('y_title', $this->default_ttfont, 12);
        } 
        // Fixed:
        else {
            $this->SetFont('generic', 2);
            $this->SetFont('title', 4);
            $this->SetFont('legend', 2);
            $this->SetFont('x_label', 1);
            $this->SetFont('y_label', 1);           
            $this->SetFont('x_title', 2);
            $this->SetFont('y_title', 2);
        }
    }
    
    /*!
     * Sets Fixed/Truetype font parameters.
     *  \param $which_elem Is the element whose font is to be changed.
     *         It can be one of 'title', 'legend','generic', 
     *         'x_label', 'y_label', x_title' or 'y_title'
     *  \param $which_font Can be a number (for fixed font sizes) or 
     *         a string with the filename when using TTFonts.
     *  \param $which_size Point size (TTF only)
     * Calculates and updates internal height and width variables.
     */
    function SetFont($which_elem, $which_font, $which_size=12) {
    
        //TTF Settings
        
        if ($this->use_ttf) {
            $path = $this->ttf_path.'/'.$which_font;
            
            if (! is_file($path) || ! is_readable($path) ) {
                $this->DrawError("SetSmallFont(): True Type font $path doesn't exist");
                return FALSE;
            }
            
            switch ($which_elem) {
                case 'generic':
                    $this->generic_ttfont = $path;
                    $this->generic_ttfont_size = $which_size;
                    break;
                case 'title':
                    $this->title_ttfont = $path;
                    $this->title_ttfont_size = $which_size;
                    break;
                case 'legend':
                    $this->legend_ttfont = $path;
                    $this->legend_ttfont_size = $which_size;
                    break;
                case 'x_label':
                    $this->x_label_ttfont = $path;
                    $this->x_label_ttfont_size = $which_size;
                    break;
                case 'y_label':
                    $this->y_label_ttfont = $path;
                    $this->y_label_ttfont_size = $which_size;
                    break;                   
                case 'x_title':
                    $this->x_title_ttfont = $path;
                    $this->x_title_ttfont_size = $which_size;
                    break;
                case 'y_title':
                    $this->y_title_ttfont = $path;
                    $this->y_title_ttfont_size = $which_size;
                    break;
                default:
                    $this->DrawError("SetFont(): Unknown element '$which_elem' specified.");
                    return FALSE;
            }
            return TRUE;
            
        } 
        
        // Non TTF Settings
        
        if ($which_font > 5 || $which_font < 0) {
            $this->DrawError("SetSmallFont(): Non-TTF font size must be 1,2,3,4 or 5");
            return FALSE;
        }
        
        switch ($which_elem) {
            case 'generic':
                $this->generic_font = $which_font;
                $this->generic_font_height = ImageFontHeight($which_font);
                $this->generic_font_width = ImageFontWidth($which_font);
                break;
            case 'title':
               $this->title_font = $which_font;
               $this->title_font_height = ImageFontHeight($which_font);
               $this->title_font_width = ImageFontWidth($which_font);
               break;
            case 'legend':
                $this->legend_font = $which_font;
                $this->legend_font_height = ImageFontHeight($which_font);
                $this->legend_font_width = ImageFontWidth($which_font);
                break;
            case 'x_label':
                $this->x_label_font = $which_font;
                $this->x_label_font_height = ImageFontHeight($which_font);
                $this->x_label_font_width = ImageFontWidth($which_font);
                break;
            case 'y_label':
                $this->y_label_font = $which_font;
                $this->y_label_font_height = ImageFontHeight($which_font);
                $this->y_label_font_width = ImageFontWidth($which_font);
                break;               
            case 'x_title':
                $this->x_title_font = $which_font;
                $this->x_title_font_height = ImageFontHeight($which_font);
                $this->x_title_font_width = ImageFontWidth($which_font);
                break;
            case 'y_title':
                $this->y_title_font = $which_font;
                $this->y_title_font_height = ImageFontHeight($which_font);
                $this->y_title_font_width = ImageFontWidth($which_font);
                break;
            default:
                $this->DrawError("SetFont(): Unknown element '$which_elem' specified.");
                return FALSE;
        }
        return TRUE;
    }
    

    
/////////////////////////////////

    function SetLineStyles($which_sls){
        $this->line_style = $which_sls;
        return true;
    }

    /*!
     * Expects an array with the names to be written in the graph's legend.
     */
    function SetLegend($which_leg){
        if (is_array($which_leg)) { 
            $this->legend = $which_leg;
            return true;
        } else { 
            $this->DrawError('Error: SetLegend argument must be an array');
            return false;
        }
    }

    /*!
     * Specifies the absolute (relative to image's up/left corner) position
     * of the legend's upper/leftmost corner.
     *  $which_type not yet used (TODO)
     */
    function SetLegendPixels($which_x,$which_y,$which_type) { 
        $this->legend_x_pos = $which_x;
        $this->legend_y_pos = $which_y;
        
        return true;
    }

    /*!
     * Specifies the relative (to graph's origin) position of the legend's
     * upper/leftmost corner. MUST be called after scales are set up.
     *   $which_type not yet used (TODO)
     */
    function SetLegendWorld($which_x,$which_y,$which_type='') { 
        if ($this->scale_is_set != 1) 
            $this->CalcTranslation();
            
        $this->legend_x_pos = $this->xtr($which_x);
        $this->legend_y_pos = $this->ytr($which_y);
        
        return true;
    }
   
    /*!
     * Sets output format.
     * TODO: Maybe a single SetOutputFormat() in which to specify file format,
     * inline condition, cacheable, etc.
     */
    function SetFileFormat($which_file_format) {
        // I know rewriting this was unnecessary, but it didn't work for me, I don't
        // understand why. (MBD)
        $asked = strtolower($which_file_format);
        switch ($asked) {
            case 'jpg':
                if (imagetypes() & IMG_JPG)
                    return TRUE;
                break;
            case 'png':
                if (imagetypes() & IMG_PNG)
                    return TRUE;
                break;
            case 'gif':
                if (imagetypes() & IMG_GIF)
                    return TRUE;
                break;
            case 'wbmp':
                if (imagetypes() & IMG_WBMP)
                    return TRUE;
                break;
            default:
                $this->PrintError("SetFileFormat(): Unrecognized option '$which_file_format'");
                return false;
        }
        $this->PrintError("SetFileFormat():File format '$which_file_format' not supported");
        return FALSE;
    }    


    function SetInputFile($which_input_file) { 
        //$this->SetFileFormat($which_frmt);
        $size = GetImageSize($which_input_file);
        $input_type = $size[2]; 

        switch($input_type) {  //After SetFileFormat is in lower case
            case "1":
                $im = @ImageCreateFromGIF ($which_input_file);
                if (!$im) { // See if it failed 
                    $this->PrintError("Unable to open $which_input_file as a GIF");
                    return false;
                }
            break;
            case "3":
                $im = @ImageCreateFromPNG ($which_input_file); 
                if (!$im) { // See if it failed 
                    $this->PrintError("Unable to open $which_input_file as a PNG");
                    return false;
                }
            break;
            case "2":
                $im = @ImageCreateFromJPEG ($which_input_file); 
                if (!$im) { // See if it failed 
                    $this->PrintError("Unable to open $which_input_file as a JPG");
                    return false;
                }
            break;
            default:
                $this->PrintError('Please select wbmp,gif,jpg, or png for image type!');
                return false;
            break;
        }

        // Set Width and Height of Image
        $this->image_width = $size[0];
        $this->image_height = $size[1];

        $this->img = $im;

        return true;

    }

    function SetOutputFile($which_output_file) { 
        $this->output_file = $which_output_file;
        return true;
    }

    /*!
     * Sets the output image as 'inline', ie. no Content-Type headers are sent
     * to the browser. Very useful if you want to embed the images.
     */
    function SetIsInline($which_ii) {
        $this->is_inline = $which_ii;
        return true;
    }
    

    /*!
     * Performs the actual outputting of the generated graph, and
     * destroys the image resource.
     */
    function PrintImage() {

        if ( ($this->browser_cache == 0) && ($this->is_inline == 0)) { //Submitted by Thiemo Nagel
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . 'GMT');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
        }

        switch($this->file_format) {
            case "png":
                if ($this->is_inline == 0) {
                    Header('Content-type: image/png');
                }
                if ($this->is_inline == 1 && $this->output_file != "") {
                    ImagePng($this->img,$this->output_file);
                } else {
                    ImagePng($this->img);
                }
                break;
            case "jpg":
                if ($this->is_inline == 0) {
                    Header('Content-type: image/jpeg');
                }
                if ($this->is_inline == 1 && $this->output_file != "") {
                    ImageJPEG($this->img,$this->output_file);
                } else {
                    ImageJPEG($this->img);
                }
                break;
            case "gif":
                if ($this->is_inline == 0) {
                    Header('Content-type: image/gif');
                }
                if ($this->is_inline == 1 && $this->output_file != "") {
                    ImageGIF($this->img,$this->output_file);
                } else {
                    ImageGIF($this->img);
                }

                break;
            case "wbmp":
                if ($this->is_inline == 0) {
                    Header('Content-type: image/wbmp');
                }
                if ($this->is_inline == 1 && $this->output_file != "") {
                    ImageWBMP($this->img,$this->output_file);
                } else {
                    ImageWBMP($this->img);
                }

                break;
            default:
                $this->PrintError('Please select an image type!<br>');
                break;
        }
        ImageDestroy($this->img);
        return true;
    }

    /*!
     * Fills the background of the image with a solid color
     * TODO: Images? Patterns?
     */
    function DrawBackground() {
        //if ($this->img == "") { $this->InitImage(); };
        if ($this->background_done == 0) { //Don't draw it twice if drawing two plots on one image
            ImageFilledRectangle($this->img, 0, 0,
                $this->image_width, $this->image_height, $this->ndx_bg_color);
            $this->background_done = 1;
        }
        return true;
    }

    /*!
     * Draws a border around the final image.
     */
    function DrawImageBorder() {
        switch ($this->image_border_type) {
            case "raised":
                ImageLine($this->img,0,0,$this->image_width-1,0,$this->ndx_i_light);
                ImageLine($this->img,1,1,$this->image_width-2,1,$this->ndx_i_light);
                ImageLine($this->img,0,0,0,$this->image_height-1,$this->ndx_i_light);
                ImageLine($this->img,1,1,1,$this->image_height-2,$this->ndx_i_light);
                ImageLine($this->img,$this->image_width-1,0,$this->image_width-1,$this->image_height-1,$this->ndx_i_dark);
                ImageLine($this->img,0,$this->image_height-1,$this->image_width-1,$this->image_height-1,$this->ndx_i_dark);
                ImageLine($this->img,$this->image_width-2,1,$this->image_width-2,$this->image_height-2,$this->ndx_i_dark);
                ImageLine($this->img,1,$this->image_height-2,$this->image_width-2,$this->image_height-2,$this->ndx_i_dark);
            break;
            case "plain":
                ImageLine($this->img,0,0,$this->image_width,0,$this->ndx_i_dark);
                ImageLine($this->img,$this->image_width-1,0,$this->image_width-1,$this->image_height,$this->ndx_i_dark);
                ImageLine($this->img,$this->image_width-1,$this->image_height-1,0,$this->image_height-1,$this->ndx_i_dark);
                ImageLine($this->img,0,0,0,$this->image_height,$this->ndx_i_dark);
            break;
            default:
            break;
        }
        return true;
    }

/////////////////////////////////////////////
///////////                              MISC
/////////////////////////////////////////////

    function SetPlotBorderType($which_pbt) {
        $this->plot_border_type = $which_pbt; //left, none, anything else=full
    }

    function SetImageBorderType($which_sibt) {
        $this->image_border_type = $which_sibt; //raised, plain
    }

    function SetDrawPlotAreaBackground($which_dpab) {
        $this->draw_plot_area_background = $which_dpab;  // 1=true or anything else=false
    }

    /*!
     * Draw Labels next to datapoints
     */
    function SetDrawPlotLabels($which_ddl) {
        $this->draw_plot_labels = $which_ddl;  // 1=true or anything else=false
    }

    /*!
     * Draw Labels (not grid labels) on X Axis, following data points
     * Care must be taken not to draw these and x_tick_labels as they'd probably overlap.
     */
    function SetDrawXDataLabels($which_dxdl) {
        $this->draw_x_data_labels = $which_dxdl;  // 1=true or anything else=false
    }

    function SetDrawYGrid($which_dyg) {
        $this->draw_y_grid = $which_dyg;  // 1=true or anything else=false
    }

    function SetDrawXGrid($which_dxg) {
        $this->draw_x_grid = $which_dxg;  // 1=true or anything else=false
    }

    function SetDashedGrid($which_dsh) {
        $this->dashed_grid = $which_dsh;    // 1 = true, 0 = false
        if ($which_dsh)
            $this->SetDashedStyle($this->ndx_light_grid_color);
    }

    function SetYGridLabelType($which_yglt) {
        $this->y_tick_label_type = $which_yglt;
        return true;
    }
    
    function SetXGridLabelType($which_xglt) {
        $this->x_tick_label_type = $which_xglt;
        return true;
    }

    function SetYGridLabelPos($which_yglp) {
        $this->y_tick_label_pos = $which_yglp;
        return true;
    }

    function SetXGridLabelPos($which_xglp) {
        $this->x_tick_label_pos = $which_xglp;
        return true;
    }
    
    /*!
     *
     */
    function SetTitle($which_title) {
        $this->title_txt = $which_title;
        
        $str = split("\n", $which_title);
        $this->title_lines = count($str);
        
        if ($this->use_ttf) {
            $size = $this->TTFBBoxSize($this->title_ttfont_size, 0, $this->title_ttfont, $which_title);
            $this->title_height = $size[0] * ($this->title_lines + 1);
        } else {
            $this->title_height = $this->title_font_height * ($this->title_lines + 1);
        }   
        return true;
    }
    
    /*!
     * Sets the X axis title.
     */
    function SetXTitle($which_xtitle) {
        $this->x_title_txt = $which_xtitle;
        
        $str = split("\n", $which_xtitle);
        $this->x_title_lines = count($str);
        
        if ($this->use_ttf) {
            $size = $this->TTFBBoxSize($this->x_title_ttfont_size, 0, $this->x_title_ttfont, $which_xtitle);
            $this->x_title_height = $size[0] * $this->x_title_lines;
        } else {
            $this->x_title_height = $this->y_title_font_height * $this->x_title_lines;
        }
 
        return true;
    }
    
   
    /*!
     * Sets the Y axis title.
     */
    function SetYTitle($which_ytitle) {
        $this->y_title_txt = $which_ytitle;
        
        $str = split("\n", $which_ytitle);
        $this->y_title_lines = count($str);
        
        if ($this->use_ttf) {
            $size = $this->TTFBBoxSize($this->y_title_ttfont_size, 90, $this->y_title_ttfont, $which_ytitle);
            $this->y_title_width = $size[1] * $this->y_title_lines;
        } else {
            $this->y_title_width = $this->y_title_font_height * $this->y_title_lines;
        }
        
        return true;
    }
    
    function SetXTitlePos($xpos) {
        $this->x_title_pos = $xpos;
        return true;
    }
    
    function SetYTitlePos($xpos) {
        $this->y_title_pos = $xpos;
        return true;
    }
   
    function SetShading($which_s) { 
        $this->shading = $which_s;
        return true;
    }
    
    function SetPlotType($which_pt) {
        $accepted = "bars,lines,linepoints,area,points,pie,thinbarline";
        $asked = trim($which_pt);
        if (eregi($asked, $accepted)) {
            $this->plot_type = $which_pt;
            return true;
        } else {
            $this->DrawError("$which_pt not an acceptable plot type");
            return false;
        }
    }

    function SetYAxisPosition($which_pos) {
        $this->y_axis_position = $which_pos;
        return true;
    }
    
    function SetXAxisPosition($which_pos) {
        $this->x_axis_position = $which_pos;
        return true;
    }

    function SetXTimeFormat($which_xtf) {
        $this->x_time_format = $which_xtf;
        return true;
    }
    function SetYTimeFormat($which_ytf) {
        $this->y_time_format = $which_ytf;
        return true;
    }
    
    function SetXDataLabelAngle($which_xdla) { 
        $this->x_label_angle = $which_xdla;
        return true;
    }
    function SetXScaleType($which_xst) { 
        $this->xscale_type = $which_xst;
        return true;
    }
    function SetYScaleType($which_yst) { 
        $this->yscale_type = $which_yst;
        if ($this->x_axis_position <= 0) { 
            $this->x_axis_position = 1;
        }
        return true;
    }

    function SetPrecisionX($which_prec) {
        $this->x_precision = $which_prec;
        return true;
    }
    function SetPrecisionY($which_prec) {
        $this->y_precision = $which_prec;
        return true;
    }

    /*!
     * \todo Move this to deprecated and use imagesetthickness()
     */
    function SetErrorBarLineWidth($which_seblw) {
        $this->error_bar_line_width = $which_seblw;
        return true;
    }

    /*!
     * \todo Move this to deprecated and use imagesetthickness()
     */
    function SetLineWidth($which_lw) {
        $this->line_width = $which_lw;
        if (!$this->error_bar_line_width) { 
            $this->error_bar_line_width = $which_lw;
        }
        return true;
    }

    function SetLabelScalePosition($which_blp) {
        //0 to 1
        $this->label_scale_position = $which_blp;
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
        SetType($which_ps,'integer');
        $this->point_size = $which_ps;

        if ($this->point_shape == "diamond" or $this->point_shape == "triangle") {
            if ($this->point_size % 2 != 0) {
                $this->point_size++;
            }
        }
        return true;
    }

    
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

   
    /*!
     * Analizes data and sets up internal maxima and minima
     * Needed by: CalcXHeights(), CalcYWidths(), (FILLME)
     */
    function FindDataLimits() {
        // Text-Data is different than data-data graphs. For them what
        // we have, instead of X values, is # of records equally spaced on data.
        // text-data is passed in as $data[] = (title,y1,y2,y3,y4,...)
        // data-data is passed in as $data[] = (title,x,y1,y2,y3,y4,...) 

        $this->number_x_points = count($this->data_values);

        switch ($this->data_type) {
            case "text-data":
                $minx = 0; //valid for BAR TYPE GRAPHS ONLY
                $maxx = $this->number_x_points - 1 ;  //valid for BAR TYPE GRAPHS ONLY
                $miny = (double) $this->data_values[0][1];
                $maxy = $miny;
                if ($this->draw_x_data_labels == '') { 
                    $this->draw_x_data_labels = 0;
                }
            break;
            default:  //Everything else: data-data, etc.
                $maxx = $this->data_values[0][1];
                $minx = $maxx;
                $miny = $this->data_values[0][2];
                $maxy = $miny;
                $maxy = $miny;
            break;
        }

        $max_records_per_group = 0;
        $total_records = 0;
        $mine = 0;  // Maximum value for the -error bar (assume error bars always > 0) 
        $maxe = 0;  // Maximum value for the +error bar (assume error bars always > 0) 
        $maxt = 0;  // Maximum number of characters in text labels
        
        reset($this->data_values);
        
        foreach($this->data_values as $dat) {   // process each row of data

            $tmp = 0;
            $total_records += count($dat) - 1; // -1 for label

            switch ($this->data_type) {
                case "text-data":
                    // Extract maximum text length
                    $val = strlen(array_shift($dat));
                    $maxt = ($val > $maxt) ? $val : $maxt;

                    //Find the relative Max and Min
                    while (list($key, $val) = each($dat)) {
                        settype($val,"double");
                        $maxy = ($val > $maxy) ? $val : $maxy;
                        $miny = ($val < $miny) ? $val : $miny;
                        $tmp++;
                    }
                    break;
                case "data-data":  
                    // X-Y data is passed in as $data[] = (title,x,y,y2,y3,...) 
                    // which you can use for multi-dimentional plots.

                    // Extract maximum text length
                    $val = strlen(array_shift($dat));
                    $maxt = ($val > $maxt) ? $val : $maxt;

                    // X value
                    $val = array_shift($dat);
                    settype($val, "double");
                    $maxx = ($val > $maxx) ? $val : $maxx;
                    $minx = ($val < $minx) ? $val : $minx;

                    while (list($key, $val) = each($dat)) {
                        settype($val,"double");
                        $maxy = ($val > $maxy) ? $val : $maxy;
                        $miny = ($val < $miny) ? $val : $miny;
                        $tmp++;
                    }
                    break;
                case "data-data-error":  //Assume 2-D for now, can go higher
                    //Regular X-Y data is passed in as $data[] = (title,x,y,error+,error-,y2,error2+,error2-)
                    
                    // Extract maximum text length
                    $val = strlen(array_shift($dat));
                    $maxt = ($val > $maxt) ? $val : $maxt;
                    
                    // X value:
                    $val = array_shift($dat);
                    settype($val, "double");
                    $maxx = ($val > $maxx) ? $val : $maxx;
                    $minx = ($val < $minx) ? $val : $minx;

                    while (list($key, $val) = each($dat)) {
                        settype($val,'double');
                        if ($key%3 == 0) {
                            $maxy = ($val > $maxy) ? $val : $maxy;
                            $miny = ($val < $miny) ? $val : $miny;
                        } elseif ($key%3 == 1)
                            $maxe = ($val > $maxe) ? $val : $maxe;
                        elseif ($key%3 == 2)
                            $mine = ($val > $mine) ? $val : $mine;
                        $tmp++;
                    }
                    $maxy = $maxy + $maxe;
                    $miny = $miny - $mine; //assume error bars are always > 0
                    break;
                default:
                    $this->PrintError('ERROR: unknown chart type');
                break;
            }
            if ($tmp > $max_records_per_group)
                $max_records_per_group = $tmp;
        }


        $this->min_x = $minx;
        $this->max_x = $maxx;
        $this->min_y = $miny;
        $this->max_y = $maxy;
        $this->max_t = $maxt;

        $this->records_per_group = ($max_records_per_group > 1) ? $max_records_per_group : 1;

        $this->data_count = $total_records;
    }


    /*!
     * Calculates image margins on the fly from title positions and sizes,
     * and tick labels positions and sizes
     */
    function CalcMargins() {

        // Upper title and tick labels
        $this->y_top_margin = $this->safe_margin + $this->title_height + $this->safe_margin;

        if ($this->x_title_pos == 'plotup' || $this->x_title_pos == 'both')
            $this->y_top_margin += $this->x_title_height + $this->safe_margin;
            
        if ($this->x_tick_label_pos == 'plotup' || $this->x_tick_label_pos == 'both')
            $this->y_top_margin += $this->x_tick_label_height + $this->htick_length * 2;

        // Lower title and tick labels
        $this->y_bot_margin = $this->safe_margin; 
        
        if ($this->x_title_pos == 'plotdown' || $this->x_title_pos == 'both')
            $this->y_bot_margin += $this->x_title_height;
            
        if ($this->x_tick_label_pos == 'plotdown' || $this->x_tick_label_pos == 'both')            
            $this->y_bot_margin += $this->x_tick_label_height + $this->htick_length * 2;

        // Left title and tick labels
        $this->x_left_margin = $this->safe_margin;
        
        if ($this->y_title_pos == 'plotleft' || $this->y_title_pos == 'both')
            $this->x_left_margin += $this->y_title_width;
            
        if ($this->y_tick_label_pos == 'plotleft' || $this->y_tick_label_pos == 'both')
            $this->x_left_margin += $this->y_tick_label_width + $this->vtick_length * 2;
            
        // Right title and tick labels
        $this->x_right_margin = $this->safe_margin;
        
        if ($this->y_title_pos == 'plotright' || $this->y_title_pos == 'both')
            $this->x_right_margin += $this->y_title_width + $this->safe_margin;
            
        if ($this->y_tick_label_pos == 'plotright' || $this->y_tick_label_pos == 'both')
            $this->x_right_margin += $this->y_tick_label_width + $this->vtick_length * 2;
        

        $this->x_tot_margin = $this->x_left_margin + $this->x_right_margin;
        $this->y_tot_margin = $this->y_top_margin + $this->y_bot_margin;

        //If data has already been analysed then set translation
        if ($this->plot_max_x && $this->plot_max_y && $this->plot_area_width ) 
            $this->CalcTranslation();
            
    }


    function SetMarginsPixels($which_lm,$which_rm,$which_tm,$which_bm) { 
        //Set the plot area using margins in pixels (left, right, top, bottom)
        $this->SetNewPlotAreaPixels($which_lm,$which_tm,($this->image_width - $which_rm),($this->image_height - $which_bm));
        return true;
    }

    function SetNewPlotAreaPixels($x1,$y1,$x2,$y2) {
        //Like in GD 0,0 is upper left set via pixel Coordinates
        $this->plot_area = array($x1,$y1,$x2,$y2);
        $this->plot_area_width = $this->plot_area[2] - $this->plot_area[0];
        $this->plot_area_height = $this->plot_area[3] - $this->plot_area[1];
        $this->y_top_margin = $this->plot_area[1];
        if ($this->plot_max_x) {
            $this->CalcTranslation();
        }
        return true;
    }

    function SetPlotAreaPixels($x1,$y1,$x2,$y2) {
        //Like in GD 0,0 is upper left
        if (!$this->x_tot_margin) {
            $this->CalcMargins();
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

        return true;

    }

    function SetPlotAreaWorld($xmin,$ymin,$xmax,$ymax) {
        if (($xmin == "")  && ($xmax == "")) {
            //For automatic setting of data we need $this->max_x
                if (!$this->max_y) {
                    $this->FindDataLimits() ;
                }
                if ($this->data_type == 'text-data') { //labels for text-data is done at data drawing time for speed.
                    $xmax = $this->max_x + 1 ;  //valid for BAR CHART TYPE GRAPHS ONLY
                    $xmin = 0 ;                 //valid for BAR CHART TYPE GRAPHS ONLY
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
        if ($this->yscale_type == "log") { 
            //extra error checking
            if ($ymin <= 0) { 
                $ymin = 1;
            } 
            if ($ymax <= 0) { 
                $this->PrintError('Log plots need data greater than 0');
            }
        }
        $this->plot_min_y = $ymin;
        $this->plot_max_y = $ymax;

        if ($ymax <= $ymin) {
            $this->DrawError('Error in Data - max not gt min');
        }

        // Set the boundaries of the box for plotting in world coord
        //if (!$this->x_tot_margin) { //We need to know the margins before we can calculate scale
        //    $this->CalcMargins();
        //    }
        
        //For this we have to reset the scale
        if ($this->plot_area_width) {
            $this->CalcTranslation();
        }

        return true;

    } //function SetPlotAreaWorld
   
   
    /*! 
     * Prints an error message to stdout and dies 
     */
    function PrintError($error_message) {
        echo "<p><b>Fatal error</b>: $error_message<p>";
        die;
    }

    /*!
     * Prints an error message inline into the generated image 
     * Defaults to centered position (MBD)
     */
    function DrawError($error_message,$where_x=NULL,$where_y=NULL) {
        if (($this->img) == "")
            $this->InitImage();
        
        $ypos = (! $where_y) ? $this->image_height/2 : $where_y;
        $xpos = (! $where_x) ? $this->image_width/2 : $where_x;
        ImageRectangle($this->img, 0,0, $this->image_width, $this->image_height,
                       ImageColorAllocate($this->img,255,255,255));
       
        if ($this->use_ttf == 1) {
            $this->DrawText($this->generic_ttfont, 0, $xpos, $ypos, ImageColorAllocate($this->img,0,0,0),
                            $this->generic_ttfont_size, $error_message, 'center', 'center');
        } else {
            $this->DrawText($this->generic_font, 0, $xpos, $ypos, ImageColorAllocate($this->img,0,0,0),
                            NULL, $error_message, 'center', 'center');
        }

        $this->PrintImage();
        return true;
    }

    /*!
     * Returns an array with the size of the bounding box of an
     * arbitrarily placed (rotated) TrueType text string.
     */
    function TTFBBoxSize($size, $angle, $font, $string) {

        //Assume angle < 90
        //echo "<b>$font</b>\n";
        $arr = ImageTTFBBox($size, 0, $font, $string);
        $flat_width  = $arr[0] - $arr[2];
        $flat_height = abs($arr[3] - $arr[5]);

            // for 90deg:
            //    $height = $arr[5] - $arr[7];
            //    $width = $arr[2] - $arr[4];

        $angle = deg2rad($angle);
        $width  = ceil(abs($flat_width*cos($angle) + $flat_height*sin($angle))); //Must be integer
        $height = ceil(abs($flat_width*sin($angle) + $flat_height*cos($angle))); //Must be integer

        return array($width, $height);
    }

    /*!
     * Calculates maximum X-Axis tick label height.
     */
    function CalcXHeights() {
        
        // TTF
        if ($this->use_ttf == 1) {
            $xstr = str_repeat('.', $this->max_t);
            $size = $this->TTFBBoxSize($this->x_label_ttfont_size, $this->x_label_angle,
                                       $this->x_label_ttfont, $xstr);
            $this->x_tick_label_height = $size[1];
        } 
        // Fixed font
        else { // For Non-TTF fonts we can have only angles 0 or 90
            if ($this->x_label_angle == 90)
                $this->x_tick_label_height = $this->max_t * $this->x_label_font_width;
            else 
                $this->x_tick_label_height = $this->x_label_font_height * 1.5;
        }

        return true;
    }


    /*!
     * Calculates Maximum Y-Axis tick label width after max_y has been 
     * set up by FindDataLimits()
     */
    function CalcYWidths() {
    
        //the "." is for space. It isn't actually printed
        $ylab = number_format($this->max_y, $this->y_precision, ".", ",") . $this->si_units . ".";
        
        // TTF
        if ($this->use_ttf == 1) {
            // Maximum Y tick label width
            $size = $this->TTFBBoxSize($this->y_label_ttfont_size, 0, $this->y_label_ttfont, $ylab);
            $this->y_tick_label_width = $size[0] * 1.5;
            
        } 
        // Fixed font
        else {
            // Y axis title width
            $this->y_tick_label_width = strlen($ylab) * $this->y_label_font_width;
        }

        return true;
    }

    /*!
     * For plots that have equally spaced x variables and multiple bars per x-point.
     */
    function SetEqualXCoord() {

        $space = ($this->plot_area[2] - $this->plot_area[0]) / ($this->number_x_points * 2) * $this->group_frac_width;
        $group_width = $space * 2;
        $bar_width = $group_width / $this->records_per_group;
        //I think that eventually this space variable will be replaced by just graphing x.
        $this->data_group_space = $space;
        $this->record_bar_width = $bar_width;
        return true;
    }



    function SetDataType($which_dt) {
        //The next three lines are for past compatibility.
        if ($which_dt == "text-linear") { $which_dt = "text-data"; };
        if ($which_dt == "linear-linear") { $which_dt = "data-data"; };
        if ($which_dt == "linear-linear-error") { $which_dt = "data-data-error"; };

        $this->data_type = $which_dt; //text-data, data-data, data-data-error
        return true;
    }

    function SetDataValues($which_dv) {
        $this->data_values = $which_dv;
//echo $this->data_values
        return true;
    }
    
/////////////////////////////////////////////    
//////////////                         COLORS
/////////////////////////////////////////////

    /*!
     * Internal Method called to set colors and preserve state
     * These are the colors of the image that are used. They are initialized
     * to work with sessions and PHP. 
     */
    function SetIndexColors() { 
        $this->ndx_i_light = $this->SetIndexColor($this->i_light);
        $this->ndx_i_dark  = $this->SetIndexColor($this->i_dark);
        $this->ndx_bg_color= $this->SetIndexColor($this->bg_color);
        $this->ndx_plot_bg_color= $this->SetIndexColor($this->plot_bg_color);

        $this->ndx_title_color= $this->SetIndexColor($this->title_color);
        $this->ndx_tick_color= $this->SetIndexColor($this->tick_color);
        $this->ndx_title_color= $this->SetIndexColor($this->label_color);
        $this->ndx_text_color= $this->SetIndexColor($this->text_color);
        $this->ndx_light_grid_color= $this->SetIndexColor($this->light_grid_color);
        $this->ndx_grid_color= $this->SetIndexColor($this->grid_color);

        reset($this->error_bar_color);  
        unset($ndx_error_bar_color);
        $i = 0; 
        while (list(, $col) = each($this->error_bar_color)) {
          $this->ndx_error_bar_color[$i] = $this->SetIndexColor($col);
            $i++;
        }
        //reset($this->data_border_color);
        unset($ndx_data_border_color);
        $i = 0;
        while (list(, $col) = each($this->data_border_color)) {
            $this->ndx_data_border_color[$i] = $this->SetIndexColor($col);
            $i++;
        }
        //reset($this->data_color); 
        unset($ndx_data_color);
        $i = 0;
        while (list(, $col) = each($this->data_color)) {
            $this->ndx_data_color[$i] = $this->SetIndexColor($col);
            $i++;
        }

        return true;
    }


    /*!
     * Sets/reverts all colors
     */
    function SetDefaultColors() {
        $this->i_light = array(194,194,194);
        $this->i_dark =  array(100,100,100);
        $this->SetPlotBgColor(array(222,222,222));
        $this->SetBackgroundColor(array(200,222,222)); //can use rgb values or "name" values
        $this->SetLabelColor('black');
        $this->SetTextColor('black');
        $this->SetGridColor('black');
        $this->SetLightGridColor(array(175,175,175));
        $this->SetTickColor('black');
        $this->SetTitleColor(array(0,0,0)); // Can be array or name
        $this->data_color = array('blue','green','yellow','red','orange','SkyBlue','violet',
                                  'azure1', 'YellowGreen','gold','orchid','maroon','plum');
        $this->error_bar_color = array('blue','green','yellow','red','orange', 'SkyBlue','violet',
                                  'azure1','YellowGreen','gold','orchid','maroon','plum');
        $this->data_border_color = array('black');

        $this->session_set = 1;          //Mark it down for PHP session() usage.
    }


    function SetBackgroundColor($which_color) {
        $this->bg_color= $which_color;
        $this->ndx_bg_color= $this->SetIndexColor($which_color);
        return true;
    }
    
    function SetPlotBgColor($which_color) {
        $this->plot_bg_color= $which_color;
        $this->ndx_plot_bg_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetTitleColor($which_color) {
        $this->title_color= $which_color;
        $this->ndx_title_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetTickColor ($which_color) {
        $this->tick_color= $which_color;
        $this->ndx_tick_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetLabelColor ($which_color) {
        $this->label_color= $which_color;
        $this->ndx_title_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetTextColor ($which_color) {
        $this->text_color= $which_color;
        $this->ndx_text_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetLightGridColor ($which_color) {
        $this->light_grid_color= $which_color;
        $this->ndx_light_grid_color= $this->SetIndexColor($which_color);
        return true;
    }

    function SetGridColor ($which_color) {
        $this->grid_color = $which_color;
        $this->ndx_grid_color= $this->SetIndexColor($which_color);
        return true;
    }


    function SetRGBArray ($which_color_array) { 
        if ( is_array($which_color_array) ) { 
            //User Defined Array
            $this->rgb_array = $which_color_array;
            return true;
        } elseif ($which_color_array == 2) { //Use the small predefined color array
        $this->rgb_array = array(
            "white"          => array(255, 255, 255),
            "snow"           => array(255, 250, 250),
            "PeachPuff"      => array(255, 218, 185),
            "ivory"          => array(255, 255, 240),
            "lavender"       => array(230, 230, 250),
            "black"          => array(  0,   0,   0),
            "DimGrey"        => array(105, 105, 105),
            "gray"           => array(190, 190, 190),
            "grey"           => array(190, 190, 190),
            "navy"           => array(  0,   0, 128),
            "SlateBlue"      => array(106,  90, 205),
            "blue"           => array(  0,   0, 255),
            "SkyBlue"        => array(135, 206, 235),
            "cyan"           => array(  0, 255, 255),
            "DarkGreen"      => array(  0, 100,   0),
            "green"          => array(  0, 255,   0),
            "YellowGreen"    => array(154, 205,  50),
            "yellow"         => array(255, 255,   0),
            "orange"         => array(255, 165,   0),
            "gold"           => array(255, 215,   0),
            "peru"           => array(205, 133,  63),
            "beige"          => array(245, 245, 220),
            "wheat"          => array(245, 222, 179),
            "tan"            => array(210, 180, 140),
            "brown"          => array(165,  42,  42),
            "salmon"         => array(250, 128, 114),
            "red"            => array(255,   0,   0),
            "pink"           => array(255, 192, 203),
            "maroon"         => array(176,  48,  96),
            "magenta"        => array(255,   0, 255),
            "violet"         => array(238, 130, 238),
            "plum"           => array(221, 160, 221),
            "orchid"         => array(218, 112, 214),
            "purple"         => array(160,  32, 240),
            "azure1"         => array(240, 255, 255),
            "aquamarine1"    => array(127, 255, 212)
            );
            return true;
        } elseif ($which_color_array == 1)  { 
            include("./rgb.inc.php"); //Get large $ColorArray
            $this->rgb_array = $RGBArray;
        } else { 
            $this->rgb_array = array("white" =>array(255,255,255), "black" => array(0,0,0));
            exit;
        }

        return true;
    }

    function SetColor($which_color) { 
        //obsoleted by SetRGBColor
        SetRgbColor($which_color);
        return true;
    }

    function SetIndexColor($which_color) { //Color is passed in as anything
          list ($r, $g, $b) = $this->SetRgbColor($which_color);  //Translate to RGB
        $index = ImageColorExact($this->img, $r, $g, $b);
        if ($index == -1) {
                  //return ImageColorAllocate($this->img, $r, $g, $b);
                  //return ImageColorClosest($this->img, $r, $g, $b);
                  return ImageColorResolve($this->img, $r, $g, $b); //requires PHP 3.0.2 and later
         } else {
                  return $index;
          }
    }
    
    function SetTransparentColor($which_color) { 
        ImageColorTransparent($this->img,$this->SetIndexColor($which_color));
        return true;
    }

    function SetRgbColor($color_asked) {
        //Returns an array in R,G,B format 0-255
        if ($color_asked == "") { $color_asked = array(0,0,0); };

        if ( count($color_asked) == 3 ) { //already array of 3 rgb
               $ret_val =  $color_asked;
        } else { // is asking for a color by string
            if(substr($color_asked,0,1) == "#") {  //asking in #FFFFFF format. 
                // [#790745] 'Hex color problem' fixed. (MBD)
                $ret_val =  array(hexdec(substr($color_asked,1,2)), hexdec(substr($color_asked,3,2)), 
                                  hexdec(substr($color_asked,5,2)));
            } else { 
                $ret_val =  $this->rgb_array[$color_asked];
            }
        }
        return $ret_val;
    }

    function SetDataColors($which_data=NULL, $which_border=NULL) {
        //Set the data to be displayed in a particular color
        if (! $which_data) {
            $which_data = array('blue','green','yellow','red','orange','SkyBlue','violet',
                                'azure1', 'YellowGreen','gold','orchid','maroon','plum');
            $which_border = array('black');
        }

        $this->data_color = $which_data;            //an array
        $this->data_border_color = $which_border;   //an array

        unset($this->ndx_data_color);
        reset($this->data_color);  //data_color can be an array of colors, one for each thing plotted
        //while (list(, $col) = each($this->data_color)) 
        $i = 0;
        while (list(, $col) = each($which_data)) {
            $this->ndx_data_color[$i] = $this->SetIndexColor($col);
            $i++;
        }

        // border_color
        //If we are also going to put a border on the data (bars, dots, area, ...)
        //    then lets also set a border color as well.
        //foreach($this->data_border_color as $col) 
        unset($this->ndx_data_border_color);
        reset($this->data_border_color);
        $i = 0;
        while (list(, $col) = each($this->data_border_color)) {
            $this->ndx_data_border_color[$i] = $this->SetIndexColor($col);
            $i++;
        }

        //Set color of the error bars to be that of data if not already set. 
        if (!$this->error_bar_color) { 
                reset($which_data);
                $this->SetErrorBarColors($which_data);
        }

        return true;

    } //function SetDataColors

    function SetErrorBarColors($which_data) {

     //Set the data to be displayed in a particular color

     if ($which_data) {
        $this->error_bar_color = $which_data;  //an array
        unset($this->ndx_error_bar_color);
        reset($this->error_bar_color);  //data_color can be an array of colors, one for each thing plotted
        $i = 0;
        while (list(, $col) = each($this->error_bar_color)) {
            $this->ndx_error_bar_color[$i] = $this->SetIndexColor($col);
            $i++;
        }
        return true;
      }
      return false;
    } //function SetErrorBarColors


    /*!
     * Sets the style for a dashed line. Defaults to 3 dots colored, 3 transparent.
     */
    function SetDashedStyle($which_ndxcol, $which_style='3-2-1-2')
    {
        switch ($which_style) {
            case '3-3':
               $style = array($which_ndxcol, $which_ndxcol, $which_ndxcol,
                              IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT);
               break;
            case '3-2-1-2':
               $style = array($which_ndxcol, $which_ndxcol, $which_ndxcol,
                              IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT,
                              $which_ndxcol,
                              IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT);
               break;
            case '1-1':
                $style = array($which_ndxcol, IMG_COLOR_TRANSPARENT);
                break;
            default:
                $this->DrawError("SetDashedStyle(): unknow style '$style' requested.");
                return false;
        }
        imagesetstyle($this->img, $style);
        return true;                               
    }
    
    
/////////////////////////////////////////////    
///////////////                         TICKS
/////////////////////////////////////////////    


   
    function SetHorizTickIncrement($which_ti) {
        //Use either this or NumHorizTicks to set where to place x tick marks
        if ($which_ti) {
            $this->horiz_tick_increment = $which_ti;  //world coordinates
        } else {
            if (!$this->max_x) {
                $this->FindDataLimits();  //Get maxima and minima for scaling
            }
            //$this->horiz_tick_increment = ( ceil($this->max_x * 1.2) - floor($this->min_x * 1.2) )/10;
            $this->horiz_tick_increment =  ($this->plot_max_x  - $this->plot_min_x  )/10;
        }
        $this->num_horiz_ticks = ''; //either use num_vert_ticks or vert_tick_increment, not both
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
        $this->num_vert_ticks = ''; //either use num_vert_ticks or vert_tick_increment, not both
        return true;
    }

    function SetNumHorizTicks($which_nt) {
        $this->num_horiz_ticks = $which_nt;
        $this->horiz_tick_increment = '';  //either use num_horiz_ticks or horiz_tick_increment, not both
        return true;
    }
    function SetNumVertTicks($which_nt) {
        $this->num_vert_ticks = $which_nt;
        $this->vert_tick_increment = '';  //either use num_vert_ticks or vert_tick_increment, not both
        return true;
    }
    
    function SetVertTickPos($which_tp) { 
        $this->vert_tick_position = $which_tp;  //plotleft, plotright, both, yaxis, none
        return true;
    }
    function SetHorizTickPos($which_tp) { 
        $this->horiz_tick_position = $which_tp; //plotdown, plotup, both, none
        return true;
    }

    function SetSkipBottomTick($which_sbt) {
        $this->skip_bottom_tick = $which_sbt;
        return true;
    }

    function SetHTickLength($which_ln) {
        $this->htick_length = $which_ln;
        return true;
    }
    
    function SetVTickLength($which_ln) {
        $this->vtick_length = $which_ln;
        return true;
    }
    
/////////////////////////////////////////////
////////////////                  TRANSLATION
/////////////////////////////////////////////

    /*!
     * Calculates translation stuff...
     */
    function CalcTranslation() {
        if ($this->xscale_type == "log") { 
            $this->xscale = ($this->plot_area_width)/(log10($this->plot_max_x) - log10($this->plot_min_x));
        } else { 
            $this->xscale = ($this->plot_area_width)/($this->plot_max_x - $this->plot_min_x);
        }
        if ($this->yscale_type == "log") { 
            $this->yscale = ($this->plot_area_height)/(log10($this->plot_max_y) - log10($this->plot_min_y));
        } else { 
            $this->yscale = ($this->plot_area_height)/($this->plot_max_y - $this->plot_min_y);
        }

        // GD defines x=0 at left and y=0 at TOP so -/+ respectively
        if ($this->xscale_type == "log") { 
            $this->plot_origin_x = $this->plot_area[0] - ($this->xscale * log10($this->plot_min_x) );
        } else { 
            $this->plot_origin_x = $this->plot_area[0] - ($this->xscale * $this->plot_min_x);
        }
        if ($this->yscale_type == "log") { 
            $this->plot_origin_y = $this->plot_area[3] + ($this->yscale * log10($this->plot_min_y));
        } else { 
            $this->plot_origin_y = $this->plot_area[3] + ($this->yscale * $this->plot_min_y);
        }

        $this->scale_is_set = 1;
    } // function CalcTranslation


    /*!
     * Translate world coordinates into pixel coordinates
     * The pixel coordinates are those of the ENTIRE image, not just the plot_area
     */
    function xtr($x_world) {
    
        //$x_pixels =  $this->x_left_margin + ($this->image_width - $this->x_tot_margin)*
        //      (($x_world - $this->plot_min_x) / ($this->plot_max_x - $this->plot_min_x)) ;
        //which with a little bit of math reduces to ...
        if ($this->xscale_type == "log") { 
            $x_pixels =  $this->plot_origin_x + log10($x_world) * $this->xscale ;
        } else { 
            $x_pixels =  $this->plot_origin_x + $x_world * $this->xscale ;
        }
        return($x_pixels);
    }


    /*!
     * Translate y world coord into pixel coord
     */
    function ytr($y_world) {
        if ($this->yscale_type == "log") { 
            $y_pixels =  $this->plot_origin_y - log10($y_world) * $this->yscale ;  //minus because GD defines y=0 at top. doh!
        } else { 
            $y_pixels =  $this->plot_origin_y - $y_world * $this->yscale ;  
        }
        return ($y_pixels);
    }

/////////////////////////////////////////////
////////////////////                  DRAWING
/////////////////////////////////////////////

    /*
    function CheckBounds(&$xpos, &$ypos) {
        $xpos = ($xpos < 0) ? 0 : (($xpos > $this->image_width) ? $this->image_width : $xpos);
        $ypos = ($ypos < 0) ? 0 : (($ypos > $this->image_height) ? $this->image_height : $ypos);
    }
    */
    
    /*!
     * Draws a string of text. Horizontal and vertical alignment are relative to 
     * to the drawing. That is: vertical text (90 deg) gets centered along y-axis 
     * with v_align='center', and adjusted to the left of x-axis with h_align = 'right',
     * 
     * \note Original multiple lines code submitted by Remi Ricard.
     * \note Original vertical code submitted by Marlin Viss.
     */
    function DrawText($which_font,$which_angle,$which_xpos,$which_ypos,$which_color,$which_size,$which_text,
                      $which_halign='left', $which_valign='bottom') {

        $height = ImageFontHeight($which_font);
        $width = ImageFontWidth($which_font);

        $interl = 2;      // Pixels between lines (TODO: add method)
        
        // TTF:  FIXME: Does alignement work for 90 deg?
        if ($this->use_ttf) { 
            $size = $this->TTFBBoxSize($which_size, $which_angle, $which_font, $which_text); 
            
            if ($which_valign == 'bottom')
                $which_ypos += $height;
            
            if ($which_halign == 'center')
                $which_xpos -= $size[0]/2;
            
            if ($which_halign == 'right')
                $which_xpos -= $size[0];
                
            ImageTTFText($this->img, $which_size, $which_angle, 
                $which_xpos, $which_ypos, $which_color, $which_font, $which_text); 
        }
        // Fixed fonts:
        else { 
            // Split the text in its lines, and count them
            $which_text = ereg_replace("\r","",$which_text);
            $str = split("\n",$which_text);
            $nlines = count($str);
            
            // Vertical text:
            // (Remember the alignment covention with vertical text)
            if ($which_angle == 90) {
                // The text goes around $which_xpos.
                if ($which_halign == 'center') 
                    $which_xpos -= ($nlines * ($height + $interl))/2;

                // Left alignment requires no modification to $xpos...
                // Right-align it. $which_xpos designated the rightmost x coordinate.
                else if ($which_halign == 'right')
                    $which_xpos += ($nlines * ($height + $interl));
                    
                $ypos = $which_ypos;
                for($i = 0; $i < $nlines; $i++) { 
                    // Center the text vertically around $which_ypos (each line)
                    if ($which_valign == 'center')
                        $ypos = $which_ypos + (strlen($str[$i]) * $width) / 2;
                    // Make the text finish (vertically) at $which_ypos
                    if ($which_valign == 'bottom')
                        $ypos = $which_ypos + strlen($str[$i]) * $width;
                        
                    ImageStringUp($this->img, $which_font, $i*($height+$interl) + $which_xpos, $ypos, $str[$i], $which_color);
                } 
            } 
            // Horizontal text:
            else {
                // The text goes above $which_ypos
                if ($which_valign == 'top')
                    $which_ypos = $which_ypos - ($nlines * ($height + $interl));
                // The text is centered around $which_ypos
                if ($which_valign == 'center')
                    $which_ypos -= ($nlines * ($height + $interl))/2;
                // valign = 'center' requires no modification
                
                $xpos = $which_xpos;
                for($i = 0; $i < $nlines; $i++) { 
                    // center the text around $which_xpos
                    if ($which_halign == 'center')
                        $xpos = $which_xpos - (strlen($str[$i]) * $width)/2;
                    // make the text finish at $which_xpos
                    if ($which_halign == 'right')
                        $xpos = $which_xpos - strlen($str[$i]) * $width;
                        
                    ImageString($this->img, $which_font, $xpos, $i*($height+$interl) + $which_ypos, $str[$i], $which_color);
                }                 
            }
        } 
        return true; 

    }


    function DrawLabels() {
        $this->DrawTitle();
        $this->DrawXTitle();
        $this->DrawYTitle();
        return true;
    }
    
    /*!
     * Adds the title to the graph.
     */
    function DrawTitle() {

        // Center of the plot area
        //$xpos = ($this->plot_area[0] + $this->plot_area_width / 2);
        
        // Center of the image:
        $xpos = $this->image_width / 2;
        
        // Place it at almost at the top
        $ypos = $this->safe_margin;
        
        if ($this->use_ttf)
            $this->DrawText($this->title_ttfont, $this->title_angle, $xpos, $ypos,
                            $this->ndx_title_color, $this->title_ttfont_size, $this->title_txt,'center'); 
        else
            $this->DrawText($this->title_font, $this->title_angle, $xpos, $ypos, 
                            $this->ndx_title_color, NULL, $this->title_txt,'center'); 
         
        return true; 

    }


    /*!
     * Draws the X-Axis Title
     */
    function DrawXTitle() {
    
        if ($this->x_title_pos == 'none')
            return;
            
        // TTF
        if ($this->use_ttf == 1) { 
            $xpos = $this->xtr(($this->plot_max_x + $this->plot_min_x)/2.0);
            
            // Upper title
            if ($this->x_title_pos == 'plotup' || $this->x_title_pos == 'both') {
                $ypos = $this->safe_margin + $this->title_height + $this->safe_margin;
                $this->DrawText($this->x_title_ttfont, $this->x_title_angle,
                                $xpos, $ypos, $this->ndx_title_color, $this->x_title_ttfont_size,
                                $this->x_title_txt,'center');
            }
            // Lower title
            if ($this->x_title_pos == 'plotdown' || $this->x_title_pos == 'both') {
                $ypos = $this->ytr($this->plot_min_y) + $this->x_tick_label_height + $this->safe_margin;
                $this->DrawText($this->x_title_ttfont, $this->x_title_angle,
                                $xpos, $ypos, $this->ndx_title_color, $this->x_title_ttfont_size, 
                                $this->x_title_txt,'center');
            }
        } 
        // Fixed font
        else { 
            $xpos = $this->xtr(($this->plot_max_x+$this->plot_min_x)/2.0);
            
            // Upper title
            if ($this->x_title_pos == 'plotup' || $this->x_title_pos == 'both') {
                $ypos = $this->safe_margin + $this->title_height + $this->safe_margin;
                $this->DrawText($this->x_title_font, $this->x_title_angle, 
                                $xpos, $ypos, $this->ndx_title_color, 0, $this->x_title_txt, 'center');
            }
            // Lower title
            if ($this->x_title_pos == 'plotdown' || $this->x_title_pos == 'both') {
                $ypos = $this->ytr($this->plot_min_y) + $this->x_tick_label_height + $this->safe_margin;
                $this->DrawText($this->x_title_font, $this->x_title_angle, 
                                $xpos, $ypos, $this->ndx_title_color, 0, $this->x_title_txt, 'center');
            }
        }        
        return true;
    }

    /*!
     * Draws the Y-Axis Title
     */
    function DrawYTitle() {
    
        if ($this->y_title_pos == 'none')
            return;

        // TTF
        if ($this->use_ttf == 1) { 
            // Center the title to the plot
            $ypos = $this->ytr(($this->plot_max_y + $this->plot_min_y)/2.0);
            
            if ($this->y_title_pos == 'plotleft' || $this->y_title_pos == 'both') {
                $xpos = $this->safe_margin;
                $this->DrawText($this->y_title_ttfont, 90, $xpos, $ypos, 
                                $this->ndx_title_color, $this->y_title_ttfont_size, $this->y_title_txt,'left','center');
            }
            if ($this->y_title_pos == 'plotright' || $this->y_title_pos == 'both') {
                $xpos = $this->image_width - $this->safe_margin - $this->y_title_width - $this->safe_margin;
                $this->DrawText($this->y_title_ttfont, 90, $xpos, $ypos, 
                                $this->ndx_title_color, $this->y_title_ttfont_size, $this->y_title_txt,'left','center');
            }
            
        } 
        // Fixed font
        else {
            // Center the title vertically to the plot (MBD FIXME: why doesn't it work?)
            //$ypos = $this->plot_area_height/2;
            $ypos = $this->image_height /2;
            if ($this->y_title_pos == 'plotleft' || $this->y_title_pos == 'both') {
                $xpos = $this->safe_margin;
                $this->DrawText($this->y_title_font, 90, $xpos, $ypos, $this->ndx_title_color, 
                                NULL, $this->y_title_txt, 'left', 'center');
            }
            if ($this->y_title_pos == 'plotright' || $this->y_title_pos == 'both') {
                $xpos = $this->image_width - $this->safe_margin - $this->y_title_width - $this->safe_margin;
                $this->DrawText($this->y_title_font, 90, $xpos, $ypos, $this->ndx_title_color, 
                                NULL, $this->y_title_txt, 'left', 'center');
            }
        }
        
        return true;
    }


    /*!
     * Fills the plot area with a solid color
     * TODO: Images? Patterns?
     */
    function DrawPlotAreaBackground() {
        ImageFilledRectangle($this->img,$this->plot_area[0],
            $this->plot_area[1],$this->plot_area[2],$this->plot_area[3],
            $this->ndx_plot_bg_color);
    }

    /*
     * 
     */
    function DrawYAxis() { 
        //Draw Line at left side or at this->y_axis_position
        if ($this->y_axis_position != "") {
            $yaxis_x = $this->xtr($this->y_axis_position);
        } else {
            $yaxis_x = $this->plot_area[0];
        }

        ImageLine($this->img, $yaxis_x, $this->plot_area[1], 
            $yaxis_x, $this->plot_area[3], $this->ndx_grid_color);

        // Draw ticks, if any
        if ($this->vert_tick_position != 'none') { 
            $this->DrawVerticalTicks();
        }

    }
    
    /*
     *
     */
    function DrawXAxis() {
        //Draw Tick and Label for Y axis
        $ylab =$this->FormatTickLabel('y',$this->x_axis_position);
        if ($this->skip_bottom_tick != 1) { 
            $this->DrawVerticalTick($ylab,$this->x_axis_position);
        }

        //Draw X Axis at Y=$x_axis_postion
        ImageLine($this->img,$this->plot_area[0]+1,$this->ytr($this->x_axis_position),
                $this->xtr($this->plot_max_x)-1,$this->ytr($this->x_axis_position),$this->ndx_tick_color);

        //X Ticks and Labels
        // FIXME: With text-data, the vertical grid never gets drawn. (MBD)
//        if ($this->data_type != 'text-data') { //labels for text-data done at data drawing time for speed.
            $this->DrawHorizontalTicks();
//        }
        return true;
    }

    /*!
     * Draw Just one Tick, called from DrawVerticalTicks()
     */
    function DrawVerticalTick($which_ylab, $which_ypos) {
    
        if ($this->y_axis_position != "") { 
            //Ticks and lables are drawn on the left border of yaxis
            $yaxis_x = $this->xtr($this->y_axis_position);
        } else { 
            //Ticks and lables are drawn on the left border of PlotArea.
            $yaxis_x = $this->plot_area[0];
        }

        $y_pixels = $this->ytr($which_ypos);

        //Lines Across the Plot Area
        if ($this->draw_y_grid == 1) {
            if ($this->dashed_grid) {
                ImageLine($this->img,$this->plot_area[0]+1,$y_pixels, $this->plot_area[2]-1,$y_pixels, IMG_COLOR_STYLED);
            } else {
                ImageLine($this->img,$this->plot_area[0]+1,$y_pixels, $this->plot_area[2]-1,
                          $y_pixels,$this->ndx_light_grid_color);
            }
        }

        //Ticks to the Left of the Plot Area
        if (($this->vert_tick_position == "plotleft") || ($this->vert_tick_position == "both") ) { 
            ImageLine($this->img,(-$this->htick_length+$yaxis_x),
                      $y_pixels,$yaxis_x,
                      $y_pixels, $this->ndx_tick_color);
        }

        //Ticks to the Right of the Plot Area
        if (($this->vert_tick_position == "plotright") || ($this->vert_tick_position == "both") ) { 
            ImageLine($this->img,($this->plot_area[2]+$this->htick_length),
                      $y_pixels,$this->plot_area[2],
                      $y_pixels,$this->ndx_tick_color);
        }
        
        //Ticks on the Y Axis 
        if (($this->vert_tick_position == "yaxis") ) { 
            ImageLine($this->img,$yaxis_x - $this->vtick_length, $y_pixels,$yaxis_x,$y_pixels,$this->ndx_tick_color);
        }

        // Labels:
        // TTF:
        if ($this->use_ttf) {
            //Labels to the left of the plot area
            if ($this->y_tick_label_pos == 'plotleft' || $this->y_tick_label_pos == 'both') {
                $this->DrawText($this->y_label_ttfont, 0, $yaxis_x - $this->vtick_length * 1.5, 
                                $y_pixels, $this->ndx_text_color, $this->y_label_ttfont_size, 
                                $which_ylab, 'right', 'center');
            }
            //Labels to the right of the plot area
            if ($this->y_tick_label_pos == 'plotright' || $this->y_tick_label_pos == 'both') {
                $this->DrawText($this->y_label_font, 0, $this->plot_area[2] + $this->vtick_length * 2,
                                $y_pixels, $this->ndx_text_color, $this->y_label_ttfont_size,
                                $which_ylab, 'left', 'center');
            }
        } 
        // Fixed Fonts:
        else {
            //Labels to the left of the plot area
            if ($this->y_tick_label_pos == 'plotleft' || $this->y_tick_label_pos == 'both') {
                $this->DrawText($this->y_label_font, 0, $yaxis_x - $this->vtick_length * 1.5, 
                                $y_pixels, $this->ndx_text_color, NULL, $which_ylab, 'right', 'center');
            }
            //Labels to the right of the plot area
            if ($this->y_tick_label_pos == 'plotright' || $this->y_tick_label_pos == 'both') {
                $this->DrawText($this->y_label_font, 0, $this->plot_area[2] + $this->vtick_length * 2,
                                $y_pixels, $this->ndx_text_color, NULL, $which_ylab, 'left', 'center');
            }
        }
   }       // Function DrawVerticalTick()


    /*!
     * Draws Grid, Ticks and Tick Labels along Y-Axis
     * Ticks and ticklabels can be left of plot only, right of plot only, 
     * both on the left and right of plot, or crossing a user defined Y-axis
     */
    function DrawVerticalTicks() {

        if ($this->skip_top_tick != 1) { //If tick increment doesn't hit the top 
            //Left Top
            //ImageLine($this->img,(-$this->tick_length+$this->xtr($this->plot_min_x)),
            //        $this->ytr($this->plot_max_y),$this->xtr($this->plot_min_x),$this->ytr($this->plot_max_y),
            //        $this->ndx_tick_color);
            //$ylab = $this->FormatYTickLabel($plot_max_y);

            //Right Top
            //ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
            //        $this->ytr($this->plot_max_y),$this->xtr($this->plot_max_x-1),$this->ytr($this->plot_max_y),
            //        $this->ndx_tick_color);

            //Draw Grid Line at Top
            if ($this->draw_y_grid == 1) {
                if ($this->dashed_grid == 1) {
                    ImageLine($this->img,$this->plot_area[0]+1,$this->ytr($this->plot_max_y),
                              $this->plot_area[2]-1,$this->ytr($this->plot_max_y), IMG_COLOR_STYLED);
                } else {
                    ImageLine($this->img,$this->plot_area[0]+1,$this->ytr($this->plot_max_y),
                              $this->plot_area[2]-1,$this->ytr($this->plot_max_y),$this->ndx_light_grid_color);
                }
            }
        }

        if ($this->skip_bottom_tick != 1) { 
            //Right Bottom
            //ImageLine($this->img,($this->xtr($this->plot_max_x)+$this->tick_length),
            //        $this->ytr($this->plot_min_y),$this->xtr($this->plot_max_x),
            //        $this->ytr($this->plot_min_y),$this->ndx_tick_color);

            //Draw Grid Line at Bottom of Plot
            if ($this->draw_y_grid == 1) {
                if ($this->dashed_grid == 1) {
                    ImageLine($this->img,$this->xtr($this->plot_min_x)+1,$this->ytr($this->plot_min_y),
                              $this->xtr($this->plot_max_x),$this->ytr($this->plot_min_y), IMG_COLOR_STYLED);
                } else {
                    ImageLine($this->img,$this->xtr($this->plot_min_x)+1,$this->ytr($this->plot_min_y),
                              $this->xtr($this->plot_max_x),$this->ytr($this->plot_min_y), $this->ndx_light_grid_color);
                }
            }
        }
        
        // maxy is always > miny so delta_y is always positive
        if ($this->vert_tick_increment) {
            $delta_y = $this->vert_tick_increment;
        } elseif ($this->num_vert_ticks) {
            $delta_y = ($this->plot_max_y - $this->plot_min_y) / $this->num_vert_ticks;
        } else {
            $delta_y =($this->plot_max_y - $this->plot_min_y) / 10 ;
        }

        $y_tmp = $this->plot_min_y;
        SetType($y_tmp,'double');
        if ($this->skip_bottom_tick == 1) { 
            $y_tmp += $delta_y;
        }

        while ($y_tmp <= $this->plot_max_y){
            //For log plots: 
            if (($this->yscale_type == "log") && ($this->plot_min_y == 1) && 
                ($delta_y%10 == 0) && ($y_tmp == $this->plot_min_y)) { 
                $y_tmp = $y_tmp - 1; //Set first increment to 9 to get: 1,10,20,30,...
            }

            $ylab = $this->FormatTickLabel('y',$y_tmp);

            $this->DrawVerticalTick($ylab,$y_tmp);

            $y_tmp += $delta_y;
        }

        return true;

    } // function DrawVerticalTicks


    /*!
     * Draws Grid, Ticks and Tick Labels along X-Axis
     * Ticks and tick labels can be down of plot only, up of plot only, 
     * both on up and down of plot [, or crossing a user defined X-axis (TODO)]
     *
     * \fixme lefmost tick stuff should be corrected, maybe introduced into the loop
     *        despite any necessary ifs (not to overwrite the axis with the grid)
     *        inside. Upper and right ticks have to be drawn too.
     * \note Original vertical code submitted by Marlin Viss
     */
    function DrawHorizontalTicks() {
    
        // Leftmost Tick
        ImageLine($this->img,$this->plot_area[0],
                $this->plot_area[3]+$this->htick_length,
                $this->plot_area[0],$this->plot_area[3],$this->ndx_tick_color);
                
        $xlab = $this->FormatTickLabel('x', 0);
        
        if ($this->use_ttf) {
            $this->DrawText($this->x_label_ttfont, $this->x_label_angle, $this->plot_area[0], 
                            $this->plot_area[3] + $this->htick_length*2, $this->ndx_text_color, 
                            $this->x_label_ttfont_size, $xlab, 'center', 'bottom');
        } else {
             $this->DrawText($this->x_label_font, $this->x_label_angle, $this->plot_area[0], 
                            $this->plot_area[3] + $this->htick_length*2, $this->ndx_text_color, 
                            NULL, $xlab, 'center', 'bottom');
        }          

        // Calculate x increment between ticks
        if ($this->horiz_tick_increment) {
            $delta_x = $this->horiz_tick_increment;
        } elseif ($this->num_horiz_ticks) {
            $delta_x = ($this->plot_max_x - $this->plot_min_x) / $this->num_horiz_ticks;
        } else {
            $delta_x =($this->plot_max_x - $this->plot_min_x) / 10 ;
        }

        $x_tmp = $this->plot_min_x + $delta_x;  //Don't overwrite left Y axis 
        SetType($x_tmp,'double');

        while ($x_tmp < $this->plot_max_x) {    // Was '<=', now '<' not to overwrite rightmost y axis
        
            $xlab = $this->FormatTickLabel('x', $x_tmp);
            $x_pixels = $this->xtr($x_tmp);

            // Bottom Tick
            if ($this->horiz_tick_position == 'plotdown' || $this->horiz_tick_position == 'both') {
                  ImageLine($this->img,$x_pixels,$this->plot_area[3] + $this->htick_length,
                    $x_pixels,$this->plot_area[3], $this->ndx_tick_color);
            }
            // Top Tick
            if ($this->horiz_tick_position == 'plotup' || $this->horiz_tick_position == 'both') {
                ImageLine($this->img, $x_pixels, $this->plot_area[1] - $this->htick_length,
                    $x_pixels, $this->plot_area[1], $this->ndx_tick_color);
            }
            
            // Vertical grid lines
            if ($this->draw_x_grid == 1) {
                if ($this->dashed_grid == 1) {
                    ImageLine($this->img,$x_pixels,$this->plot_area[1], $x_pixels,$this->plot_area[3], 
                              IMG_COLOR_STYLED);
                } else {
                    ImageLine($this->img,$x_pixels,$this->plot_area[1], $x_pixels,$this->plot_area[3], 
                              $this->ndx_light_grid_color);
                }        
            }

            // X Axis tick labels
            // TTF:
            if ($this->use_ttf) {
                if ($this->x_tick_label_pos == 'plotdown' || $this->x_tick_label_pos == 'both') {
                    $this->DrawText($this->x_label_ttfont, $this->x_label_angle, $x_pixels, 
                                    $this->plot_area[3] + $this->htick_length * 2, $this->ndx_text_color, 
                                    $this->x_label_ttfont_size, $xlab, 'center', 'bottom');
                }

                if ($this->x_tick_label_pos == 'plotup' || $this->x_tick_label_pos == 'both') {
                    $this->DrawText($this->x_label_ttfont, $this->x_label_angle, $x_pixels, 
                                    $this->plot_area[1] - $this->htick_length * 2, $this->ndx_text_color, 
                                    $this->x_label_ttfont_size, $xlab, 'center', 'top');
                }
            } 
            // Fixed fonts:
            else {
                if ($this->x_tick_label_pos == 'plotdown' || $this->x_tick_label_pos == 'both') {
                    $this->DrawText($this->x_label_font, $this->x_label_angle, $x_pixels, 
                                    $this->plot_area[3] + $this->htick_length * 2, $this->ndx_text_color, 
                                    NULL, $xlab, 'center', 'bottom');
                }

                if ($this->x_tick_label_pos == 'plotup' || $this->x_tick_label_pos == 'both') {
                    $this->DrawText($this->x_label_font, $this->x_label_angle, $x_pixels, 
                                    $this->plot_area[1] - $this->htick_length * 2, $this->ndx_text_color, 
                                    NULL, $xlab, 'center', 'top');
                }
            }
            $x_tmp += $delta_x;
        }

    } // function DrawHorizontalTicks

    /*!
     * 
     */
    function DrawPlotBorder() {
        switch ($this->plot_border_type) {
            case "left" :
                ImageLine($this->img, $this->plot_area[0],$this->ytr($this->plot_min_y),
                    $this->plot_area[0],$this->ytr($this->plot_max_y),$this->ndx_grid_color);
            break;
            case "none":
                //Draw No Border
            break;
            default:
                ImageRectangle($this->img, $this->plot_area[0],$this->ytr($this->plot_min_y),
                    $this->plot_area[2],$this->ytr($this->plot_max_y),$this->ndx_grid_color);
            break;
        }
        $this->DrawYAxis();
        $this->DrawXAxis();
        return true;
    }

    /*!
     * [ MBD mié nov 19 19:34:11 CET 2003 ]
     */
    function FormatTickLabel($which_axis, $which_lab) { 
        if ($which_axis == 'x') {
            switch ($this->x_tick_label_type) {
                case "title":
                    $lab = $this->data_values[$which_lab][0];
                    break;
                case "data":
                    $lab = number_format($this->plot_min_x,$this->x_precision,".",",") . "$this->si_units";
                    break;
                case "none":
                    $lab = '';
                    break;
                case "time":  //Time formatting suggested by Marlin Viss
                    $lab = strftime($this->x_time_format,$this->plot_min_x);
                    break;
                default:
                    //Unchanged from whatever format is passed in
                    // FIXME!
                    $lab = $which_lab; //$this->plot_min_x;
                break;
            }
        } else if ($which_axis == 'y') {
            switch ($this->y_tick_label_type) {
                case "data":
                    $lab = number_format($which_lab, $this->y_precision,".",",") . "$this->si_units";
                    break;
                case "none":
                    $lab = '';
                    break;
                case "time":
                    $lab = strftime($this->y_time_format, $which_lab);
                    break;
                case "right":
                    //Make it right aligned
                    $lab = str_pad($which_lab,$this->y_margin_width," ",STR_PAD_LEFT);
                    break;
                default:
                    //Unchanged from whatever format is passed in
                    $lab = $which_lab;
                    break;
            }
        }
        
        return($lab);

    } //function FormatTickLabel

   
    /*!
     * Draws the data label associated with a point in the plot.
     * This is different from x_labels drawn by DrawHorizontalTicks() and care
     * should be taken not to draw both, as they'd probably overlap.
     * Calling of this function in DrawLines(), etc is triggered by draw_datalabels
     */
    function DrawXDataLabel($xlab,$xpos) {
        if ($this->use_ttf) {
            $this->DrawText($this->x_label_ttfont, $this->x_label_angle, $xpos, $this->plot_area[3], 
                            $this->ndx_text_color, $this->x_label_ttfont_size, $xlab, 'center', 'bottom');       
        } else {
            $this->DrawText($this->x_label_font, $this->x_label_angle, $xpos, $this->plot_area[3], 
                            $this->ndx_text_color, NULL, $xlab, 'center', 'bottom');
        }
    }
    
    /*!
     * Draws the graph legend
     *
     * \note Base code submitted by Marlin Viss
     */
    function DrawLegend($which_x1,$which_y1,$which_boxtype) {
    
        // Find maximum legend label length
        $max_legend_length = 0;
        
        foreach ($this->legend as $leg) {
            $len = strlen($leg);
            if ($max_legend_length < $len) {
                $max_legend_length = $len;
            }
        }

        $line_spacing = 4;                                  // In pixels
        $v_margin = $this->legend_font_height/2;            // Between vertical borders and labels
        $dot_height = $this->legend_font_height + $line_spacing;    // Small boxes' size
        $width = $this->legend_font_width*($max_legend_length+4);   // Legend's with.
        
        // upper Left
        if ( (! $which_x1) || (! $which_y1) ) {
            $box_start_x = $this->plot_area[2] - $width;
            $box_start_y = $this->plot_area[1] + 4;
        } else { 
            $box_start_x = $which_x1;
            $box_start_y = $which_y1;
        }

        // Lower right corner
        $box_end_y = $box_start_y + $dot_height*(count($this->legend)) + 2*$v_margin; 
        $box_end_x = $box_start_x + $width - 5;


        // Draw outer box
        ImageFilledRectangle($this->img, $box_start_x, $box_start_y,$box_end_x, $box_end_y, $this->ndx_bg_color);
        ImageRectangle($this->img, $box_start_x, $box_start_y,$box_end_x, $box_end_y, $this->ndx_grid_color);

        $color_index=0;
        $max_color_index = count($this->ndx_data_color) - 1;
        
        $dot_left_x = $box_end_x - $this->legend_font_width * 2;
        $dot_right_x = $box_end_x - $this->legend_font_width;
        $y_pos = $box_start_y + $v_margin;
        
        foreach ($this->legend as $leg) {
            // Text right aligned to the little box
            $this->DrawText($this->legend_font, 0, $dot_left_x, $y_pos, $this->ndx_text_color,
                            NULL, $leg, 'right');
            // Draw a box in the data color
            ImageFilledRectangle($this->img, $dot_left_x, $y_pos + 1, $dot_right_x,
                                 $y_pos + $dot_height-1, $this->ndx_data_color[$color_index]);
            // Draw a rectangle around the box
            ImageRectangle($this->img, $dot_left_x, $y_pos + 1, $dot_right_x,
                           $y_pos + $dot_height-1, $this->ndx_text_color);
                
            $y_pos += $this->legend_font_height + $line_spacing;
            
            $color_index++;
            if ($color_index > $max_color_index) 
                $color_index=0;
        }
    } // Function DrawLegend

   
    /*!
     *
     */
    function DrawPieChart() {
        //$pi = '3.14159265358979323846';
        $xpos = $this->plot_area[0] + $this->plot_area_width/2;
        $ypos = $this->plot_area[1] + $this->plot_area_height/2;
        $diameter = (min($this->plot_area_width, $this->plot_area_height)) ;
        $radius = $diameter/2;

        ImageArc($this->img, $xpos, $ypos, $diameter, $diameter, 0, 360, $this->ndx_grid_color);

        $total = 0;
        reset($this->data_values);
        $tmp = $this->number_x_points - 1;
        while (list($j, $row) = each($this->data_values)) {
            //Get sum of each type
            $color_index = 0;
            $i = 0;
            //foreach ($row as $v) 
            while (list($k, $v) = each($row)) {
                if ($k != 0) {
                    if ($j == 0) { 
                        $sumarr[$i] = $v;
                    } elseif ($j < $tmp) { 
                        $sumarr[$i] += $v;
                    } else { 
                        $sumarr[$i] += $v;
                    // NOTE!  sum > 0 to make pie charts
                        $sumarr[$i] = abs($sumarr[$i]); 
                        $total += $sumarr[$i];
                    }
                }
            $i++;
            }
        }
        
        $color_index = 0;
        $start_angle = 0;

        reset($sumarr);
        $end_angle = 0;
        while (list(, $val) = each($sumarr)) {
            if ($color_index >= count($this->ndx_data_color)) 
                $color_index=0;  //data_color = array
            // Bugfix #549839 (MBD)
            if (! $total) {
                $label_txt = '0%';
                $val = 0;
            } else {    
                $label_txt = number_format(($val / $total * 100), $this->y_precision, ".", ",") . "%";
                $val = 360 * ($val / $total);
            }
            $end_angle += $val;
            $mid_angle = $end_angle - ($val / 2);

            $slicecol = $this->ndx_data_color[$color_index];

            //Need this again for FillToBorder
            ImageArc($this->img, $xpos, $ypos, $diameter, $diameter, 0, 360, $this->ndx_grid_color);

            $out_x = $radius * cos(deg2rad($end_angle));
            $out_y = - $radius * sin(deg2rad($end_angle));

            $mid_x = $xpos + ($radius/2 * cos(deg2rad($mid_angle))) ;
            $mid_y = $ypos + (- $radius/2 * sin(deg2rad($mid_angle)));

            // FIXME? The '* 1.2' trick is to get labels a little more outwards so they
            // get better centered in small sectors. It might screw things up sometimes (?) (MBD)
            $label_x = $xpos + ($radius*1.2 * cos(deg2rad($mid_angle))) * $this->label_scale_position;
            $label_y = $ypos + (- $radius*1.2 * sin(deg2rad($mid_angle))) * $this->label_scale_position;

            $out_x = $xpos + $out_x;
            $out_y = $ypos + $out_y;

            ImageLine($this->img, $xpos, $ypos, $out_x, $out_y, $this->ndx_grid_color);
            //ImageLine($this->img, $xpos, $ypos, $label_x, $label_y, $this->ndx_grid_color);
            ImageFillToBorder($this->img, $mid_x, $mid_y, $this->ndx_grid_color, $slicecol);

            if ($this->use_ttf) {
                $this->DrawText($this->generic_ttfont, 0, $label_x, $label_y, $this->ndx_grid_color,
                                $this->generic_ttfont_size, $label_txt, 'center', 'center');
            } else {
                $this->DrawText($this->generic_font, 0, $label_x, $label_y, $this->ndx_grid_color,
                                NULL, $label_txt, 'center', 'center');           
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
                if ($key == 0) {
                    $lab = $val;
                } elseif ($key == 1) {
                    $x_now = $val;
                    $x_now_pixels = $this->xtr($x_now); //Use a bit more memory to save 2N operations.
                } elseif ($key%3 == 2) {
                    $y_now = $val;
                    $y_now_pixels = $this->ytr($y_now);

                    //Draw Data Label
                    if ( $this->draw_plot_labels == 1) {
                        // TODO: Check if this works (MBD)
                        $this->DrawText($this->generic_font, 0, $x_now, $y_now, $this->ndx_text_color, 7,
                                        $lab, 'center', 'top');
                    }

                    if ($color_index >= count($this->ndx_data_color)) { $color_index=0;};
                    $barcol = $this->ndx_data_color[$color_index];
                    $error_barcol = $this->ndx_error_bar_color[$color_index];

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
                    $this->DrawYErrorBar($x_now,$y_now,$val,$this->error_bar_shape,$error_barcol);
                } elseif ($key%3 == 1) {
                    $this->DrawYErrorBar($x_now,$y_now,-$val,$this->error_bar_shape,$error_barcol);
                }
            }
        }
    }

    function DrawDotsError() {
        //Draw Dots - data comes in as array("title",x,y,error+,error-,y2,error2+,error2-,...);
        reset($this->data_values);
        while (list(, $row) = each($this->data_values)) {
            $color_index = 0;
            //foreach ($row as $v) 
            while (list($key, $val) = each($row)) {
                if ($key == 0) {
                } elseif ($key == 1) {
                    $xpos = $val;
                } elseif ($key%3 == 2) {
                    if ($color_index >= count($this->ndx_data_color)) $color_index=0;
                    $barcol = $this->ndx_data_color[$color_index];
                    $error_barcol = $this->ndx_error_bar_color[$color_index];
                    $ypos = $val;

                    $color_index++;
                    $this->DrawDot($xpos,$ypos,$this->point_shape,$barcol);
                } elseif ($key%3 == 0) {
                    $this->DrawYErrorBar($xpos,$ypos,$val,$this->error_bar_shape,$error_barcol);
                } elseif ($key%3 == 1) {
                    $mine = $val ;
                    $this->DrawYErrorBar($xpos,$ypos,-$val,$this->error_bar_shape,$error_barcol);
                }
            }
        }

    }

    function DrawDots() {
        //Draw Dots - data comes in as array("title",x,y1,y2,y3,...);
        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {
            $color_index = 0;
            //foreach ($row as $v) 
            while (list($k, $v) = each($row)) {
                if ($k == 0) {
                } elseif (($k == 1) && ($this->data_type == "data-data"))  { 
                    $xpos = $v;
                } else {
                    if ($this->data_type == "text-data") { 
                        $xpos = ($j+.5); 
                    } 
                    if ($color_index >= count($this->ndx_data_color)) $color_index=0;
                    $barcol = $this->ndx_data_color[$color_index];

                    //if (is_numeric($v))  //PHP4 only
                    if ((strval($v) != "") ) {   //Allow for missing Y data 
                        $this->DrawDot($xpos,$v,$this->point_shape,$barcol);
                    }
                    $color_index++;
                }
            }
        }

    } //function DrawDots

    function DrawDotSeries() {
        //Depreciated: Use DrawDots
        $this->DrawDots();
    }

    function DrawThinBarLines() {
        //A clean,fast routine for when you just want charts like stock volume charts
        //Data must be text-data since I didn't see a graphing need for equally spaced thin lines. 
        //If you want it - then write to afan@jeo.net and I might add it. 

        if ($this->data_type != "data-data") { $this->DrawError('Data Type for ThinBarLines must be data-data'); };
        $y1 = $this->ytr($this->x_axis_position);

        reset($this->data_values);
        while (list(, $row) = each($this->data_values)) {
            $color_index = 0;
            while (list($k, $v) = each($row)) {
                if ($k == 0) {
                        $xlab = $v;
                } elseif ($k == 1) {
                    $xpos = $this->xtr($v);
                    if ( ($this->draw_x_data_labels == 1) )  { //See "labels_note1 above.
                        $this->DrawXDataLabel($xlab,$xpos);
                    }
                } else {
                    if ($color_index >= count($this->ndx_data_color)) $color_index=0;
                    $barcol = $this->ndx_data_color[$color_index];

                    ImageLine($this->img,$xpos,$y1,$xpos,$this->ytr($v),$barcol);
                    $color_index++;
                }
            }
        }

    }  //function DrawThinBarLines

    function DrawYErrorBar($x_world,$y_world,$error_height,$error_bar_type,$color) {
        $x1 = $this->xtr($x_world);
        $y1 = $this->ytr($y_world);
        $y2 = $this->ytr($y_world+$error_height) ;

        for ($width = 0; $width < $this->error_bar_line_width; $width++) {
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

    function DrawArea() {
        //Data comes in as $data[]=("title",x,y,...);
        //Set first and last datapoints of area
        $i = 0;
        while ($i < $this->records_per_group) {
            $posarr[$i][] =  $this->xtr($this->min_x);    //x initial
            $posarr[$i][] =  $this->ytr($this->x_axis_position);     //y initial
            $i++;
        }

        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {
            $color_index = 0;
            //foreach ($row as $v)
            while (list($k, $v) = each($row)) {
                if ($k == 0) {
                    //Draw Data Labels
                    $xlab = SubStr($v,0,$this->max_t);
                } elseif ($k == 1) {
                    $x = $this->xtr($v);
                    // DrawXDataLabel interferes with Numbers on x-axis
                    //$this->DrawXDataLabel($xlab,$x);
                } else {
                    // Create Array of points for later

                    $y = $this->ytr($v);
                    $posarr[$color_index][] = $x;
                    $posarr[$color_index][] = $y;
                    $color_index++;
                }
            }
        }

        //Final_points
        for ($i = 0; $i < $this->records_per_group; $i++) {
            $posarr[$i][] =  $this->xtr($this->max_x);            //x final
            $posarr[$i][] =  $this->ytr($this->x_axis_position);     //y final
           }

        $color_index=0;

        //foreach($posarr as $row)
        reset($posarr);
        while (list(, $row) = each($posarr)) {
            if ($color_index >= count($this->ndx_data_color)) $color_index=0;
            $barcol = $this->ndx_data_color[$color_index];
            ImageFilledPolygon($this->img, $row, (count($row)) / 2, $barcol);
            $color_index++;
        }

    }

    function DrawAreaSeries() {

        //Set first and last datapoints of area
        $i = 0;
        while ($i < $this->records_per_group) {
            $posarr[$i][] =  $this->xtr(.5);            //x initial
            $posarr[$i][] =  $this->ytr($this->x_axis_position);     //y initial
            $i++;
        }

        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {
            $color_index = 0;
            //foreach ($row as $v)
            while (list($k, $v) = each($row)) {
                if ($k == 0) {
                    //Draw Data Labels
                    $xlab = SubStr($v,0,$this->max_t);
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
            $posarr[$i][] =  round($this->xtr($this->max_x + .5));    //x final
            $posarr[$i][] =  $this->ytr($this->x_axis_position);         //y final
           }

        $color_index=0;

        //foreach($posarr as $row)
        reset($posarr);
        while (list(, $row) = each($posarr)) {
            if ($color_index >= count($this->ndx_data_color)) $color_index=0;
            $barcol = $this->ndx_data_color[$color_index];
//echo "$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12], $barcol<br>";
            ImageFilledPolygon($this->img, $row, (count($row)) / 2, $barcol);
            $color_index++;
        }

    }

    function DrawLines() {
        //Data comes in as $data[]=("title",x,y,...);
        $start_lines = 0;
        if ($this->data_type == "text-data") { 
            $lastx[0] = $this->xtr(0);
            $lasty[0] = $this->xtr(0);
        }

        //foreach ($this->data_values as $row)
        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {

            $color_index = 0;
            $i = 0; 
            //foreach ($row as $v)
            while (list($k, $v) = each($row)) {
                if ($k == 0) { 
                    $xlab = SubStr($v,0,$this->max_t);
                } elseif (($k == 1) && ($this->data_type == "data-data"))  { 
                        $x_now = $this->xtr($v);
                } else {
                    //(double) $v;
                    // Draw Lines
                    if ($this->data_type == "text-data") { 
                        $x_now = $this->xtr($j+.5); 
                    } 

                    //if (is_numeric($v))  //PHP4 only
                    if ((strval($v) != "") ) {   //Allow for missing Y data 
                        $y_now = $this->ytr($v);
                        if ($color_index >= count($this->ndx_data_color)) { $color_index=0;} ;
                        $barcol = $this->ndx_data_color[$color_index];

                        if ($this->line_style[$i] == "dashed") {
                            imagesetstyle($this->img, array($barcol, $barcol, $barcol, $barcol,
                                        IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT));
                        }
                        if ($start_lines == 1) {
                            for ($width = 0; $width < $this->line_width; $width++) {
                                if ($this->line_style[$i] == "dashed") {
                                    ImageLine($this->img, $x_now, $y_now + $width, $lastx[$i], $lasty[$i] + $width, 
                                              IMG_COLOR_STYLED);
                                } else {
                                    ImageLine($this->img, $x_now, $y_now + $width, $lastx[$i], $lasty[$i] + $width, $barcol);
                                }
                            }
                        }
                        $lastx[$i] = $x_now;
                    } else { 
                        $y_now = $lasty[$i];
                        //Don't increment lastx[$i]
                    }
                    //$bordercol = $this->ndx_data_border_color[$colbarcount];

                    $lasty[$i] = $y_now;
                    $color_index++;
                    $i++;
                }
                //Now we are assured an x_value
                if ( ($this->draw_x_data_labels == 1) && ($k == 1) )  { //See "labels_note1 above.
                    $this->DrawXDataLabel($xlab,$x_now);
                }
            } //while rows of data
            $start_lines = 1;
        }
    }

        //Data comes in as $data[]=("title",x,y,e+,e-,y2,e2+,e2-,...);

    function DrawLineSeries() {
        //This function is replaced by DrawLines
        //Tests have shown not much improvement in speed by having separate routines for DrawLineSeries and DrawLines
        //For ease of programming I have combined them
        return false;
    } //function DrawLineSeries


    function DrawBars() {

        if ($this->data_type != "text-data") { 
            $this->DrawError('Bar plots must be text-data: use function SetDataType("text-data")');
        }

        $xadjust = ($this->records_per_group * $this->record_bar_width )/4;

        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {

            $color_index = 0;
            $colbarcount = 0;
            $x_now = $this->xtr($j+.5);

            while (list($k, $v) = each($row)) {
                if ($k == 0) {
                    //Draw Data Labels
                    $xlab = SubStr($v,0,$this->max_t);
                    $this->DrawXDataLabel($xlab,$x_now);
                } else {
                    // Draw Bars ($v)
                    $x1 = $x_now - $this->data_group_space + ($k-1)*$this->record_bar_width;
                    $x2 = $x1 + $this->record_bar_width*$this->bar_width_adjust; 

                    if ($v < $this->x_axis_position) {
                        $y1 = $this->ytr($this->x_axis_position);
                        $y2 = $this->ytr($v);
                    } else {
                        $y1 = $this->ytr($v);
                        $y2 = $this->ytr($this->x_axis_position);
                    }

                    if ($color_index >= count($this->ndx_data_color)) $color_index=0;
                    if ($colbarcount >= count($this->ndx_data_border_color)) $colbarcount=0;
                    $barcol = $this->ndx_data_color[$color_index];
                    $bordercol = $this->ndx_data_border_color[$colbarcount];

                    if ((strval($v) != "") ) {   //Allow for missing Y data 
                        if ($this->shading > 0) {
                            for($i=0;$i<($this->shading);$i++) { 
                            //Shading set in SetDefaultColors
                            ImageFilledRectangle($this->img, $x1+$i, $y1-$i, $x2+$i, $y2-$i, $this->ndx_i_light);
                            }
                        }

                        ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $barcol);
                        ImageRectangle($this->img, $x1, $y1, $x2, $y2, $bordercol);
                        if ($this->draw_plot_labels == '1') {  //ajo
                            $y1 = $this->ytr($this->label_scale_position * $v);
                            $this->DrawText($this->x_title_ttfont, $this->x_title_angle,
                                $x1+$this->record_bar_width/2, $y1, $this->ndx_title_color, 
                                $this->x_title_ttfont_size, $v,'center','top');
                        }
                    } 

                    $color_index++;
                    $colbarcount++;
                }
            }
        }
    } //function DrawBars    

    function DrawGraph() {

        if ($this->img == "") {
            $this->DrawError('DrawGraph(): No image resource allocated');
            return;
        }

        if (! is_array($this->data_values)) {
            $this->DrawBackground();
            $this->DrawError("DrawGraph(): No array of data in \$data_values");
        } else {
            // Didn't the constructor already set data_color? (MBD)
            /*
            if (! $this->data_color) {
                $this->SetDataColors(array('blue','green','yellow','red','orange','blue'),array('black'));
            }
            */
            $this->FindDataLimits();        //Get maxima and minima for scaling
            // Check for empty data sets:
            if ($this->data_count == 0) {
                $this->DrawError('Empty data set');
                if ($this->print_image == 1) { 
                    $this->PrintImage();
                }
                return;
            }

            $this->CalcXHeights();      // Get data for bottom/upper margin

            $this->CalcYWidths();       // Get data for left/right margin

            $this->CalcMargins();        // Calculate margins

            if (!$this->plot_area_width) 
                $this->SetPlotAreaPixels('','','','');        //Set Margins

            if (!$this->plot_max_y)   //If not set by user call SetPlotAreaWorld,
                $this->SetPlotAreaWorld('','','','');

            if ($this->data_type == "text-data")
                $this->SetEqualXCoord();

            $this->SetPointSize($this->point_size);

            $this->DrawBackground();
            
            $this->DrawImageBorder();

            $this->CalcTranslation();

            if ($this->draw_plot_area_background == 1)
                $this->DrawPlotAreaBackground();
           
            //$foo = "$this->max_y, $this->min_y, $new_miny, $new_maxy, $this->x_margin_height";
            //ImageString($this->img, 4, 20, 20, $foo, $this->ndx_text_color);

            switch ($this->plot_type) {
                case "bars":
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    $this->DrawBars();
                    $this->DrawXAxis();
                    break;
                case "thinbarline":
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    $this->DrawThinBarLines();
                    break;
                case "lines":
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    if ( $this->data_type == "text-data") {
                        $this->DrawLines();
                    } elseif ( $this->data_type == "data-data-error") {
                        $this->DrawLinesError();
                    } else {
                        $this->DrawLines();
                    }
                    break;
                case "area":
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    if ( $this->data_type == "text-data") {
                        $this->DrawAreaSeries();
                    } else {
                        $this->DrawArea();
                    }
                    break;
                case "linepoints":
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    if ( $this->data_type == "text-data") {
                        $this->DrawLines();
                        $this->DrawDots();
                    } elseif ( $this->data_type == "data-data-error") {
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
                    if ( $this->data_type == "text-data") {
                        $this->DrawDots();
                    } elseif ( $this->data_type == "data-data-error") {
                        $this->DrawDotsError();
                    } else {
                        $this->DrawDots();
                    }
                    break;
                case "pie":
                    $this->DrawPieChart();
                    $this->DrawLabels();
                    break;
                default:
                    $this->DrawPlotBorder();
                    $this->DrawLabels();
                    $this->DrawBars();
                    break;
            }

            if ($this->legend) {
                $this->DrawLegend($this->legend_x_pos,$this->legend_y_pos,'');
            }
        }
        if ($this->print_image == 1) { 
            $this->PrintImage();
        }
    } //function DrawGraph

/////////////////////////////////////////////
//////////////////                 DEPRECATED
/////////////////////////////////////////////
   
    /*!
     * Deprecated, use SetVertTickPos('none') instead.
     */
    function SetDrawVertTicks($which_dvt) {
        if ($which_dvt != 1)
            $this->SetVertTickPos('none');
        return true;
    } 
        
    /*!
     * Deprecated, use SetHorizTickPos('none') instead.
     */
    function SetDrawHorizTicks($which_dht) {
        if ($which_dht != 1)
           $this->SetHorizTickPos('none');
        return true;
    }
    
    /*!
     * Deprecated, use SetFont() instead.
     */
    function SetTitleFontSize($which_size) {
        return $this->SetFont('title', $which_size);
    }
    
    /*!
     * Deprecated, use SetFont() instead.
     */
    function SetAxisFontSize($which_size) {
        $this->SetFont('x_label', $which_size);
        $this->SetFont('y_label', $whic_size);
    }
    
    /*!
     * Deprecated, use SetFont() instead.
     */
    function SetSmallFontSize($which_size) {
        return $this->SetFont('generic', $which_size);
    }

    /*!
     * Deprecated, use SetFont() instead.
     */
    function SetXLabelFontSize($which_size) {
        return $this->SetFont('x_title', $which_size);
    }
    
    /*!
     * Deprecated, use SetFont() instead.
     */
    function SetYLabelFontSize($which_size) {
        return $this->SetFont('y_title', $which_size);
    }

    /*!
     * Deprecated, use SetXTitle() instead.
     */ 
    function SetXLabel($which_xlab) {
        return $this->SetXTitle($which_xlab);
    }

    /*!
     * Deprecated, use SetYTitle() instead.
     */ 
    function SetYLabel($which_ylab) {
        return $this->SetYTitle($which_ylab);
    }   
    /*!
     * Deprecated, use SetVertTickPos() instead.
     */
    function SetVertTickPosition($which_tp) { 
        return $this->SetVertTickPosition($which_tp); 
    }
    
    /*!
     * Deprecated, use SetVertTickPos() instead.
     */
    function SetHorizTickPosition($which_tp) { 
        return $this->SetHorizTickPosition($which_tp); 
    }

    /*!
     * Deprecated.
     */
    function SetImageArea($which_iw,$which_ih) {
        //Note this is now an Internal function - please set w/h via PHPlot()
        $this->image_width = $which_iw;
        $this->image_height = $which_ih;

        return true;
    }

    /*!
     * Deprecated, use SetHTickLength() and SetVTickLength() instead.
     */
    function SetTickLength($which_tl) {
        $this->SetHTickLength($which_tl);
        $this->SetVTickLength($which_tl);
        return true;
    }




}  // class PHPlot

?>
