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


// PHPLOT Version 4.?.?
// Requires PHP 4.1.0 or later (CHECK THIS)
//*****************                       __FUNCTION__ needs PHP 4.3.0

error_reporting(E_ALL);
//require_once('debug.php');


class PHPlot {

    /* I have removed internal variable declarations, some isset() checking was required, 
     * but now the variables left are those which can be tweaked by the user. This is intended to
     * be the first step towards moving most of the Set...() methods into a subclass which will be 
     * used only when strictly necessary. Many users will be able to put default values here in the
     * class and thus avoid memory overhead and reduce parsing times.
     */
    //////////////// CONFIG PARAMETERS //////////////////////
    
    var $is_inline = FALSE;             // FALSE = Sends headers, TRUE = sends just raw image data
    var $browser_cache = FALSE;         // FALSE = Sends headers for browser to not cache the image,
                                        // (only if is_inline = FALSE also)

    var $safe_margin = 5;               // Extra margin used in several places. In pixels
    
    var $x_axis_position = 0;           // Where to draw the X_axis (world coordinates)
    var $y_axis_position = '';          // Leave blank for Y axis at left of plot. (world coord.)

    var $xscale_type = 'linear';        // linear, log
    var $yscale_type = 'linear';
    
//Fonts
    var $use_ttf  = FALSE;                  // Use True Type Fonts?
    var $ttf_path = '.';                    // Default path to look in for TT Fonts. 
    var $default_ttfont = 'benjamingothic.ttf';
    var $line_spacing = 4;                  // Pixels between lines.

    // Font angles: 0 or 90 degrees for fixed fonts, any for TTF
    var $x_label_angle = 0;                 // Labels on X axis (tick and data)
    var $x_title_angle = 0;                 // Don't change this if you don't want to screw things up!
    var $y_title_angle = 90;                // Nor this.
    var $title_angle = 0;                   // Or this.
    
//Formats
    var $file_format = 'png';
    var $output_file = '';                  // For output to a file instead of stdout

//Colors and styles
    var $shading = 0;                       // 0 for no shading, > 0 is size of shadows in pixels
    var $color_array = 'small';             // 'small', 'large' or array (define your own colors)
                                            // See rgb.inc.php and SetRGBArray()

    var $draw_plot_area_background = FALSE;
    var $image_border_type = 'none';        // 'raised', 'plain', 'none'
    var $draw_broken_lines = TRUE;          // Tells not to draw lines for missing Y data.

//Data
    var $data_type = 'text-data';           // text-data, data-data-error, data-data, text-data-pie
    var $plot_type= 'linepoints';           // bars, lines, linepoints, area, points, pie, thinbarline, squared
    var $line_width = 1;                    // width of plot lines
    var $line_style = NULL;                 // array of 'solid' or 'dashed' lines
    
    var $label_scale_position = 0.5;        // Shifts data labes in pie charts. 1 = top, 0 = bottom
    var $group_frac_width = 0.7;            // value from 0 to 1 = width of bar groups
    var $bar_width_adjust = 1;              // 1 = bars of normal width, must be > 0

    var $point_size = 5;
    var $point_shape = 'diamond';           // rect, circle, diamond, triangle, dot, line, halfline, cross
    var $error_bar_shape = 'tee';           // 'tee' or 'line'
    var $error_bar_size = 5;                // right left size of tee
    var $error_bar_line_width = 1;
    var $error_bar_color = '';

    var $plot_border_type = 'full';         // left, none, full
    
    var $y_precision = 1;
    var $x_precision = 1;

    var $data_units_text = '';              // Units text for 'data' labels (i.e: '¤', '$', etc.)
    
// Titles
    var $title_txt = '';
    
    var $x_title_txt = '';
    var $x_title_pos = 'plotdown';          // plotdown, plotup, both, none
    
    var $y_title_txt = '';
    var $y_title_pos = 'plotleft';          // plotleft, plotright, both, none


//Labels
    // There are two types of labels in PHPlot:
    //    Tick labels: they follow the grid, next to ticks in axis.   (DONE)
    //                 they are drawn at grid drawing time, by _DrawXTicks() and _DrawYTicks()
    //    Data labels: they follow the data points, and can be placed on the axis or the plot (x/y)  (TODO)
    //                 they are drawn at graph plotting time, by DrawDataLabel(), called by DrawLines(), etc.
    //                 Draw*DataLabel() also draws H/V lines to datapoints depending on draw_*_data_label_line
    
    // Tick Labels
    var $x_tick_label_pos = 'plotdown';         // plotdown, plotup, both, none
    var $y_tick_label_pos = 'plotleft';         // plotleft, plotright, both, yaxis, none

   
    // Data Labels:
    var $x_data_label_pos = 'plotdown';     // plotdown, plotup, both, plot, all, none
    var $y_data_label_pos = 'plotleft';     // plotleft, plotright, both, plot, all, none
   
    var $draw_x_data_label_lines = FALSE;   // Draw a line from the data point to the axis? TODO 
    var $draw_y_data_label_lines = FALSE;
    
    // Label types: (for tick, data and plot labels)
    var $x_label_type = '';                 // data, title, time. Leave blank for no formatting.
    var $y_label_type = '';                 // data, time. Leave blank for no formatting.
    var $x_time_format = "%H:%m:%s";        // See http://www.php.net/manual/html/function.strftime.html
    var $y_time_format = "%H:%m:%s";        // SetYTimeFormat() too... 
    
    // Legend
    var $legend = '';                       // An array with legend titles
    var $legend_x_pos = '';
    var $legend_y_pos = '';


//Tick Formatting
    var $x_tick_length = 5;                 // pixels: tick length for upper/lower axis
    var $y_tick_length = 5;                 // pixels: tick length for left/right axis
    
    var $num_y_ticks = '';
    var $y_tick_increment = '';             // Set num_y_ticks or y_tick_increment, not both.
    var $y_tick_pos = 'plotleft';               // plotright, plotleft, both, yaxis, none 
    
    var $num_x_ticks = '';
    var $x_tick_increment = '';             // Set num_x_ticks or x_tick_increment, not both.
    var $x_tick_pos = 'plotdown';               // plotdown, plotup, both, none 
    
    var $skip_top_tick = FALSE;
    var $skip_bottom_tick = FALSE;

//Grid Formatting
    var $draw_x_grid = FALSE;
    var $draw_y_grid = TRUE;

    var $dashed_grid = TRUE;

    /////////////////////// INTERNAL VARIABLES /////////////////////
    
/*  
//Use for multiple plots per image TODO TODO
    var $print_image = TRUE;                // Used for multiple charts per image.


    var $image_width;                       // Total width in Pixels
    var $image_height;                      // Total height in Pixels

    var $session_set = FALSE;               // Do not change
    var $scale_is_set = FALSE;              // Do not change
    
    var $x_left_margin;
    var $y_top_margin;
    var $x_right_margin;
    var $y_bot_margin;

    var $plot_area = array(5, 5, 600, 400);
    
    // These are hashes with keys 'font', 'size', 'width', and 'height'
    // You need only to worry about SetFont() usage
    var $generic_font;
    var $title_font;
    var $legend_font;
    var $x_label_font;
    var $y_label_font;
    var $x_title_font;
    var $y_title_font;
    
    var $bg_color;
    var $plot_bg_color;
    var $grid_color;
    var $light_grid_color;
    var $very_light_grid_color;             // TODO: to be used by DrawDataLabels()'s lines
    var $tick_color;
    var $title_color;
    var $label_color;
    var $text_color;
    var $i_light = '';


    var $data_color = NULL;                 // array, to be set by the constructor
    var $data_border_color = NULL;          // array, to be set by the constructor

    var $data_values;
    var $data_count;
    
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

    var $x_tick_label_height;
    var $y_tick_label_width;
*/

//////////////////////////////////////////////////////
//BEGIN CODE
//////////////////////////////////////////////////////

    /*!
     * Constructor: Setup Img pointer, Colors and Size of Image, and font sizes
     */
    function PHPlot($which_width=600, $which_height=400, $which_output_file=NULL, $which_input_file=NULL) 
    {
        /*
         * Please see http://www.php.net/register_shutdown_function
         * PLEASE NOTE: register_shutdown_function() will take a copy of the object rather than a reference
         * so we put an ampersand. However, the function registered will work on the object as it
         * was upon registration. To solve this, one of two methods can be used:
         *      $obj = new object();
         *      register_shutdown_function(array(&$obj,'shutdown'));
         * OR
         *      $obj = &new object();
         * HOWEVER, as the second statement assigns $obj a reference to the current object, it might be that
         * several instances mess things up... (CHECK THIS)
         *
         * AND
         *    as $this->img is set upon construction of the object, problems will not arise for us (for the
         *    moment maybe, so I put all this here just in case)
         */
        register_shutdown_function(array(&$this, '_PHPlot'));

        $this->SetRGBArray($this->color_array);

        $this->background_done = FALSE;     // Set to TRUE after background image first drawn

        if ($which_output_file)
            $this->SetOutputFile($which_output_file);

        if ($which_input_file)
            $this->SetInputFile($which_input_file) ;
        else {
            $this->image_width = $which_width;
            $this->image_height = $which_height;

            $this->img = ImageCreate($this->image_width, $this->image_height);
            if (! $this->img)
                $this->PrintError("PHPlot(): Could not create image resource");

        }

        // For sessions
        if ( isset($this->session_set) ) {
            // Do nothing
        } else {
            $this->_SetDefaultColors();
        }

        $this->_SetIndexColors();

        // Init some font-related variables:
        $this->SetDefaultFonts();
        $this->SetTitle('');
        $this->SetXTitle('');
        $this->SetYTitle('');

        //Solid or dashed lines
        $this->line_style = array('solid', 'solid', 'dashed', 'dashed', 'solid', 'solid', 'dashed', 'dashed');

        //$this->SetDefaultDashedStyle('3-3');
        $this->default_dashed_style = 'array($which_ndxcol, $which_ndxcol, $which_ndxcol, '.
                                      'IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT)';
        
        // Set point size
        $this->SetPointSize($this->point_size);

        // Set up internal variables:
        $this->print_image = TRUE;      // Use for multiple plots per image TODO TODO
    }

    /*!
     * Destructor. Image resources not deallocated can be memory hogs, I think
     * it is safer to automatically call imagedestroy upon script termination than
     * do it ourselves.
     */
    function _PHPlot () 
    {
        ImageDestroy($this->img);
        return;
    }

 
/////////////////////////////////////////////    
//////////////                         COLORS
/////////////////////////////////////////////

    /*!
     * Internal Method called to set colors and preserve state
     * These are the colors of the image that are used. They are initialized
     * to work with sessions and PHP. 
     */
    function _SetIndexColors() 
    { 
        $this->ndx_i_light = $this->_SetIndexColor($this->i_light);
        $this->ndx_i_dark  = $this->_SetIndexColor($this->i_dark);
        $this->ndx_bg_color= $this->_SetIndexColor($this->bg_color);
        $this->ndx_plot_bg_color= $this->_SetIndexColor($this->plot_bg_color);

        $this->ndx_title_color= $this->_SetIndexColor($this->title_color);
        $this->ndx_tick_color= $this->_SetIndexColor($this->tick_color);
        $this->ndx_title_color= $this->_SetIndexColor($this->label_color);
        $this->ndx_text_color= $this->_SetIndexColor($this->text_color);
        $this->ndx_light_grid_color= $this->_SetIndexColor($this->light_grid_color);
        $this->ndx_grid_color= $this->_SetIndexColor($this->grid_color);

        unset($ndx_error_bar_color);
        $i = 0; 
        foreach($this->error_bar_color as $col) {
          $this->ndx_error_bar_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }
        
        unset($ndx_data_border_color);
        $i = 0;
        foreach($this->data_border_color as $col) {
            $this->ndx_data_border_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }

        unset($ndx_data_color);
        $i = 0;
        foreach ($this->data_color as $col) {
            $this->ndx_data_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }
        
        unset ($ndx_data_dark_color);
        $i = 0;
        foreach ($this->data_color as $col)
            $this->ndx_data_dark_color[$i++] = $this->_SetIndexDarkColor($col);
            
        return TRUE;
    }

    /*!
     * Returns an index to a color passed in as anything (string, hex, rgb)
     */
    function _SetIndexColor($which_color) 
    {
        list ($r, $g, $b) = $this->_SetRGBColor($which_color);  //Translate to RGB
        $index = ImageColorExact($this->img, $r, $g, $b);
        if ($index == -1) {
            return ImageColorResolve($this->img, $r, $g, $b);
        } else {
            return $index;
        }
    }
    
    /*!
     * Returns an index to a slightly darker color than that requested. 
     */
    function _SetIndexDarkColor($which_color) 
    {
        list ($r, $g, $b) = $this->_SetRGBColor($which_color);
        /* 
        $r = max(0, $r - 0x30);
        $g = max(0, $g - 0x30);
        $b = max(0, $b - 0x30);
        */
        $r -= 0x30;     $r = ($r < 0) ? 0 : $r;
        $g -= 0x30;     $g = ($g < 0) ? 0 : $g;
        $b -= 0x30;     $b = ($b < 0) ? 0 : $b;
        $index = ImageColorExact($this->img, $r, $g, $b);
        if ($index == -1) {
            return ImageColorResolve($this->img, $r, $g, $b);
        } else {
            return $index;
        }
    }

    /*!
     * Sets/reverts all colors to their defaults.
     */
    function _SetDefaultColors() 
    {
        $this->i_light = array(194, 194, 194);
        $this->i_dark =  array(100, 100, 100);
        $this->SetPlotBgColor(array(222, 222, 222));
        $this->SetBackgroundColor(array(200, 222, 222)); //can use rgb values or "name" values
        $this->SetLabelColor('black');
        $this->SetTextColor('black');
        $this->SetGridColor('black');
        $this->SetLightGridColor(array(175, 175, 175));
        $this->SetTickColor('black');
        $this->SetTitleColor(array(0, 0, 0)); // Can be array or name
        $this->data_color = array('blue', 'green', 'yellow', 'red', 'orange', 'SkyBlue', 'violet',
                                  'azure1', 'YellowGreen', 'gold', 'orchid', 'maroon', 'plum');
        $this->error_bar_color = array('blue', 'green', 'yellow', 'red', 'orange', 'SkyBlue', 'violet',
                                  'azure1', 'YellowGreen', 'gold', 'orchid', 'maroon', 'plum');
        $this->data_border_color = array('black');

        $this->session_set = TRUE;          //Mark it down for PHP session() usage.
    }


    function SetBackgroundColor($which_color) 
    {
        $this->bg_color= $which_color;
        $this->ndx_bg_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }
    
    function SetPlotBgColor($which_color) 
    {
        $this->plot_bg_color= $which_color;
        $this->ndx_plot_bg_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetTitleColor($which_color) 
    {
        $this->title_color= $which_color;
        $this->ndx_title_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetTickColor ($which_color) 
    {
        $this->tick_color= $which_color;
        $this->ndx_tick_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetLabelColor ($which_color) 
    {
        $this->label_color= $which_color;
        $this->ndx_title_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetTextColor ($which_color) 
    {
        $this->text_color= $which_color;
        $this->ndx_text_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetLightGridColor ($which_color) 
    {
        $this->light_grid_color= $which_color;
        $this->ndx_light_grid_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }

    function SetGridColor ($which_color) 
    {
        $this->grid_color = $which_color;
        $this->ndx_grid_color= $this->_SetIndexColor($which_color);
        return TRUE;
    }


    function SetRGBArray ($which_color_array) 
    { 
        if ( is_array($which_color_array) ) { 
            //User Defined Array
            $this->rgb_array = $which_color_array;
            return TRUE;
        } elseif ($which_color_array == 'small') { //Use the small predefined color array
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
            return TRUE;
        } elseif ($which_color_array === 'large')  { 
            include("./rgb.inc.php"); //Get large $ColorArray
            $this->rgb_array = $RGBArray;
        } else { 
            $this->rgb_array = array("white" =>array(255, 255, 255), "black" => array(0, 0, 0));
            // MBD: why was this here?
            //exit;
        }

        return TRUE;
    }

   
    function SetTransparentColor($which_color) 
    { 
        ImageColorTransparent($this->img, $this->_SetIndexColor($which_color));
        return TRUE;
    }

    /*!
     * Returns an array in R, G, B format 0-255
     *   \param color_asked Array(R,G,B) or string in '#AABBCC' format
     */
    function _SetRGBColor($color_asked) 
    {
        if ($color_asked == "") { $color_asked = array(0, 0, 0); };

        if ( count($color_asked) == 3 ) { //already array of 3 rgb
               $ret_val =  $color_asked;
        } else { // is asking for a color by string
            if(substr($color_asked, 0, 1) == "#") {  //asking in #FFFFFF format. 
                $ret_val =  array(hexdec(substr($color_asked, 1, 2)), hexdec(substr($color_asked, 3, 2)), 
                                  hexdec(substr($color_asked, 5, 2)));
            } else { 
                $ret_val =  $this->rgb_array[$color_asked];
            }
        }
        return $ret_val;
    }

    /*!
     * Sets the colors for the data.
     */
    function SetDataColors($which_data = NULL, $which_border = NULL) 
    {
        if (! $which_data) {
            $which_data = array('blue', 'green', 'yellow', 'red', 'orange', 'SkyBlue', 'violet',
                                'azure1', 'YellowGreen', 'gold', 'orchid', 'maroon', 'plum');
            $which_border = array('black');
        }

        $this->data_color = $which_data;            //an array
        $this->data_border_color = $which_border;   //an array

        unset($this->ndx_data_color);
        reset($this->data_color);  //data_color can be an array of colors, one for each thing plotted
        //while (list(, $col) = each($this->data_color)) 
        $i = 0;
        while (list(, $col) = each($which_data)) {
            $this->ndx_data_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }

        // border_color
        // If we are also going to put a border on the data (bars, dots, area, ...)
        // then lets also set a border color as well.
        unset($this->ndx_data_border_color);
        $i = 0;
        foreach($this->data_border_color as $col) {
            $this->ndx_data_border_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }

        //Set color of the error bars to be that of data if not already set. 
        if (!$this->error_bar_color) { 
                reset($which_data);
                $this->SetErrorBarColors($which_data);
        }

        return TRUE;

    } //function SetDataColors

    /*!
     * Sets the colors for the data error bars.
     */
    function SetErrorBarColors($which_data) 
    {
     if ($which_data) {
        $this->error_bar_color = $which_data;           // An array
        
        unset($this->ndx_error_bar_color);
        $i = 0;
        foreach($this->error_bar_color as $col) {
            $this->ndx_error_bar_color[$i] = $this->_SetIndexColor($col);
            $i++;
        }
        return TRUE;
      }
      return FALSE;
    } //function SetErrorBarColors


    /*!
     * Sets the default dashed style.
     *  \param which_style A string specifying order of colored and transparent dots, 
     *         i.e: '4-3' means 4 colored, 3 transparent; 
     *              '2-3-1-2' means 2 colored, 3 transparent, 1 colored, 2 transparent.
     */
    function SetDefaultDashedStyle($which_style) 
    {
        // String: "numcol-numtrans-numcol-numtrans..."
        $asked = explode('-', $which_style);

        if (count($asked) < 2) {
            $this->_DrawError("SetDefaultDashedStyle(): Wrong parameter '$which_style'.");
            return FALSE;
        }
        
        // Build the string to be eval()ed later by _SetDashedStyle()
        $this->default_dashed_style = "array( ";
        
        $t = 0;
        foreach($asked as $s) {
            if ($t % 2 == 0) {
                $this->default_dashed_style .= str_repeat('$which_ndxcol,', $s);
            } else {
                $this->default_dashed_style .= str_repeat('IMG_COLOR_TRANSPARENT,', $s);
            }
            $t++;
        }
        // Remove trailing comma and add closing parenthesis
        $this->default_dashed_style = substr($this->default_dashed_style, 0, -1);
        $this->default_dashed_style .= ")";

        return TRUE;
    }
    
    /*!
     * Sets the style before drawing a dashed line. Defaults to $this->default_dashed_style
     *   \param which_ndxcol Color index to be used.
     */
    function _SetDashedStyle($which_ndxcol)
    {
        eval ("\$style = $this->default_dashed_style;");
        return imagesetstyle($this->img, $style);
    }
    

/////////////////////////////////////////////
//////////////                          FONTS
/////////////////////////////////////////////


    /*!
     * Sets number of pixels between lines of the same text.
     */
    function SetLineSpacing($which_spc) 
    {
        $this->line_spacing = $which_spc;
    }

    
    /*!
     * Enables use of TrueType fonts in the graph. Font initialisation methods
     * depend on this setting, so when called, SetUseTTF() resets the font
     * settings
     */
    function SetUseTTF($which_ttf) 
    {
        $this->use_ttf = $which_ttf;
        if ($which_ttf)
            $this->SetDefaultFonts();
        return TRUE;
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
     * Sets the default TrueType font and updates all fonts to that.
     */
    function SetDefaultTTFont($which_font) 
    {
        if (is_file($which_font) && is_readable($which_font)) {
            $this->default_ttfont = $which_font;
            return $this->SetDefaultFonts();
        } else {
            $this->PrintError("SetDefaultTTFont(): $which_font is not a valid font file.");
            return FALSE;
        }
    }
        
    /*!
     * Sets fonts to their defaults
     */
    function SetDefaultFonts() 
    {
        // FIXME: check return values?
        // TTF:
        if ($this->use_ttf) {
            //$this->SetTTFPath(dirname($_SERVER['PHP_SELF']));
            $this->SetTTFPath(getcwd());
            $this->SetFont('generic', $this->default_ttfont, 8);
            $this->SetFont('title', $this->default_ttfont, 14);
            $this->SetFont('legend', $this->default_ttfont, 8);
            $this->SetFont('x_label', $this->default_ttfont, 6);
            $this->SetFont('y_label', $this->default_ttfont, 6);
            $this->SetFont('x_title', $this->default_ttfont, 10);
            $this->SetFont('y_title', $this->default_ttfont, 10);
        } 
        // Fixed:
        else {
            $this->SetFont('generic', 2);
            $this->SetFont('title', 5);
            $this->SetFont('legend', 2);
            $this->SetFont('x_label', 1);
            $this->SetFont('y_label', 1);           
            $this->SetFont('x_title', 3);
            $this->SetFont('y_title', 3);
        }
        
        return TRUE;
    }
    
    /*!
     * Sets Fixed/Truetype font parameters.
     *  \param $which_elem Is the element whose font is to be changed.
     *         It can be one of 'title', 'legend', 'generic', 
     *         'x_label', 'y_label', x_title' or 'y_title'
     *  \param $which_font Can be a number (for fixed font sizes) or 
     *         a string with the filename when using TTFonts.
     *  \param $which_size Point size (TTF only)
     * Calculates and updates internal height and width variables.
     */
    function SetFont($which_elem, $which_font, $which_size = 12) 
    {
        // TTF:
        if ($this->use_ttf) {
            $path = $this->ttf_path.'/'.$which_font;
            
            if (! is_file($path) || ! is_readable($path) ) {
                $this->_DrawError("SetFont(): True Type font $path doesn't exist");
                return FALSE;
            }
            
            switch ($which_elem) {
            case 'generic':
                $this->generic_font['font'] = $path;
                $this->generic_font['size'] = $which_size;
                break;
            case 'title':
                $this->title_font['font'] = $path;
                $this->title_font['size'] = $which_size;
                break;
            case 'legend':
                $this->legend_font['font'] = $path;
                $this->legend_font['size'] = $which_size;
                break;
            case 'x_label':
                $this->x_label_font['font'] = $path;
                $this->x_label_font['size'] = $which_size;
                break;
            case 'y_label':
                $this->y_label_font['font'] = $path;
                $this->y_label_font['size'] = $which_size;
                break;                   
            case 'x_title':
                $this->x_title_font['font'] = $path;
                $this->x_title_font['size'] = $which_size;
                break;
            case 'y_title':
                $this->y_title_font['font'] = $path;
                $this->y_title_font['size'] = $which_size;
                break;
            default:
                $this->_DrawError("SetFont(): Unknown element '$which_elem' specified.");
                return FALSE;
            }
            return TRUE;
            
        } 
        
        // Fixed fonts:
        if ($which_font > 5 || $which_font < 0) {
            $this->_DrawError("SetFont(): Non-TTF font size must be 1, 2, 3, 4 or 5");
            return FALSE;
        }
        
        switch ($which_elem) {
        case 'generic':
            $this->generic_font['font'] = $which_font;
            $this->generic_font['height'] = ImageFontHeight($which_font);
            $this->generic_font['width'] = ImageFontWidth($which_font);
            break;
        case 'title':
           $this->title_font['font'] = $which_font;
           $this->title_font['height'] = ImageFontHeight($which_font);
           $this->title_font['width'] = ImageFontWidth($which_font);
           break;
        case 'legend':
            $this->legend_font['font'] = $which_font;
            $this->legend_font['height'] = ImageFontHeight($which_font);
            $this->legend_font['width'] = ImageFontWidth($which_font);
            break;
        case 'x_label':
            $this->x_label_font['font'] = $which_font;
            $this->x_label_font['height'] = ImageFontHeight($which_font);
            $this->x_label_font['width'] = ImageFontWidth($which_font);
            break;
        case 'y_label':
            $this->y_label_font['font'] = $which_font;
            $this->y_label_font['height'] = ImageFontHeight($which_font);
            $this->y_label_font['width'] = ImageFontWidth($which_font);
            break;               
        case 'x_title':
            $this->x_title_font['font'] = $which_font;
            $this->x_title_font['height'] = ImageFontHeight($which_font);
            $this->x_title_font['width'] = ImageFontWidth($which_font);
            break;
        case 'y_title':
            $this->y_title_font['font'] = $which_font;
            $this->y_title_font['height'] = ImageFontHeight($which_font);
            $this->y_title_font['width'] = ImageFontWidth($which_font);
            break;
        default:
            $this->_DrawError("SetFont(): Unknown element '$which_elem' specified.");
            return FALSE;
        }
        return TRUE;
    }
    

    /*!
     * Returns an array with the size of the bounding box of an
     * arbitrarily placed (rotated) TrueType text string.
     */
    function _TTFBBoxSize($size, $angle, $font, $string) 
    {
        // First, assume angle < 90
        $arr = ImageTTFBBox($size, 0, $font, $string);
        $flat_width  = $arr[2] - $arr[0];
        $flat_height = abs($arr[3] - $arr[5]);

        // Now the bounding box
        $angle = deg2rad($angle);
        $width  = ceil(abs($flat_width*cos($angle) + $flat_height*sin($angle))); //Must be integer
        $height = ceil(abs($flat_width*sin($angle) + $flat_height*cos($angle))); //Must be integer
        
        return array($width, $height);
    }


    /*!
     * Draws a string of text. Horizontal and vertical alignment are relative to 
     * to the drawing. That is: vertical text (90 deg) gets centered along y-axis 
     * with v_align = 'center', and adjusted to the left of x-axis with h_align = 'right',
     * 
     * \note Original multiple lines code submitted by Remi Ricard.
     * \note Original vertical code submitted by Marlin Viss.
     *
     * \TODO: passing $which_font array by value is probably quite slow. Use pass by reference.
     */
    function DrawText($which_font, $which_angle, $which_xpos, $which_ypos, $which_color, $which_text,
                      $which_halign = 'left', $which_valign = 'bottom') {

        // TTF:
        if ($this->use_ttf) { 
            $size = $this->_TTFBBoxSize($which_font['size'], $which_angle, $which_font['font'], $which_text); 
            $rads = deg2rad($which_angle);

            if ($which_valign == 'center')
                $which_ypos += $size[1]/2;

            if ($which_valign == 'bottom')
                $which_ypos += $size[1];

            if ($which_halign == 'center')
                $which_xpos -= $size[0]/2;
            
            if ($which_halign == 'left')
                $which_xpos += $size[0]*sin($rads);
                
            if ($which_halign == 'right')
                $which_xpos -= $size[0]*cos($rads);
                
            ImageTTFText($this->img, $which_font['size'], $which_angle, 
                         $which_xpos, $which_ypos, $which_color, $which_font['font'], $which_text); 
        }
        // Fixed fonts:
        else { 
            // Split the text by its lines, and count them
            $which_text = ereg_replace("\r", "", $which_text);
            $str = split("\n", $which_text);
            $nlines = count($str);
            
            // Vertical text:
            // (Remember the alignment convention with vertical text)
            if ($which_angle == 90) {
                // The text goes around $which_xpos.
                if ($which_halign == 'center') 
                    $which_xpos -= ($nlines * ($which_font['height'] + $this->line_spacing))/2;

                // Left alignment requires no modification to $xpos...
                // Right-align it. $which_xpos designated the rightmost x coordinate.
                else if ($which_halign == 'right')
                    $which_xpos += ($nlines * ($which_font['height'] + $this->line_spacing));
                    
                $ypos = $which_ypos;
                for($i = 0; $i < $nlines; $i++) { 
                    // Center the text vertically around $which_ypos (each line)
                    if ($which_valign == 'center')
                        $ypos = $which_ypos + (strlen($str[$i]) * $which_font['width']) / 2;
                    // Make the text finish (vertically) at $which_ypos
                    if ($which_valign == 'bottom')
                        $ypos = $which_ypos + strlen($str[$i]) * $which_font['width'];
                        
                    ImageStringUp($this->img, $which_font['font'],
                                  $i * ($which_font['height'] + $this->line_spacing) + $which_xpos,
                                  $ypos, $str[$i], $which_color);
                } 
            } 
            // Horizontal text:
            else {
                // The text goes above $which_ypos
                if ($which_valign == 'top')
                    $which_ypos -= $nlines * ($which_font['height'] + $this->line_spacing);
                // The text is centered around $which_ypos
                if ($which_valign == 'center')
                    $which_ypos -= ($nlines * ($which_font['height'] + $this->line_spacing))/2;
                // valign = 'bottom' requires no modification
                
                $xpos = $which_xpos;
                for($i = 0; $i < $nlines; $i++) { 
                    // center the text around $which_xpos
                    if ($which_halign == 'center')
                        $xpos = $which_xpos - (strlen($str[$i]) * $which_font['width'])/2;
                    // make the text finish at $which_xpos
                    if ($which_halign == 'right')
                        $xpos = $which_xpos - strlen($str[$i]) * $which_font['width'];
                        
                    ImageString($this->img, $which_font['font'], $xpos, 
                                $i * ($which_font['height'] + $this->line_spacing) + $which_ypos,
                                $str[$i], $which_color);
                }                 
            }
        } 
        return TRUE; 

    }


/////////////////////////////////////////////
///////////            INPUT / OUTPUT CONTROL
/////////////////////////////////////////////

    /*!
     * Sets output format.
     * TODO: Maybe a single SetOutputFormat() in which to specify file format,
     * inline condition, cacheable, etc.
     */
    function SetFileFormat($which_file_format) 
    {
        // I know rewriting this was unnecessary, but it didn't work for me, I don't
        // understand why. 
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
            return FALSE;
        }
        $this->PrintError("SetFileFormat():File format '$which_file_format' not supported");
        return FALSE;
    }    


    /*!
     * Selects an input file to be used as background for the whole graph.
     */
    function SetInputFile($which_input_file) 
    { 
        $size = GetImageSize($which_input_file);
        $input_type = $size[2]; 

        switch($input_type) {
        case 1:
            $im = @ImageCreateFromGIF ($which_input_file);
            if (!$im) { // See if it failed 
                $this->PrintError("Unable to open $which_input_file as a GIF");
                return FALSE;
            }
        break;
        case 3:
            $im = @ImageCreateFromPNG ($which_input_file); 
            if (!$im) { // See if it failed 
                $this->PrintError("Unable to open $which_input_file as a PNG");
                return FALSE;
            }
        break;
        case 2:
            $im = @ImageCreateFromJPEG ($which_input_file); 
            if (!$im) { // See if it failed 
                $this->PrintError("Unable to open $which_input_file as a JPG");
                return FALSE;
            }
        break;
        default:
            $this->PrintError('SetInputFile(): Please select gif, jpg, or png for image type!');
            return FALSE;
        break;
        }

        // Set Width and Height of Image
        $this->image_width = $size[0];
        $this->image_height = $size[1];

        // Deallocate any resources previously allocated
        if ($this->img)
            imagedestroy($this->img);
            
        $this->img = $im;

        return TRUE;

    }

    function SetOutputFile($which_output_file) 
    { 
        $this->output_file = $which_output_file;
        return TRUE;
    }

    /*!
     * Sets the output image as 'inline', ie. no Content-Type headers are sent
     * to the browser. Very useful if you want to embed the images.
     */
    function SetIsInline($which_ii) 
    {
        $this->is_inline = $which_ii;
        return TRUE;
    }
    

    /*!
     * Performs the actual outputting of the generated graph, and
     * destroys the image resource.
     */
    function _PrintImage() 
    {
        // Browser cache stuff submitted by Thiemo Nagel
        if ( (! $this->browser_cache) && (! $this->is_inline)) {
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . 'GMT');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
        }

        switch($this->file_format) {
        case 'png':
            if (! $this->is_inline) {
                Header('Content-type: image/png');
            }
            if ($this->is_inline && $this->output_file != "") {
                ImagePng($this->img, $this->output_file);
            } else {
                ImagePng($this->img);
            }
            break;
        case 'jpg':
            if (! $this->is_inline) {
                Header('Content-type: image/jpeg');
            }
            if ($this->is_inline && $this->output_file != "") {
                ImageJPEG($this->img, $this->output_file);
            } else {
                ImageJPEG($this->img);
            }
            break;
        case 'gif':
            if (! $this->is_inline) {
                Header('Content-type: image/gif');
            }
            if ($this->is_inline && $this->output_file != "") {
                ImageGIF($this->img, $this->output_file);
            } else {
                ImageGIF($this->img);
            }

            break;
        case 'wbmp':        // wireless bitmap, 2 bit.
            if (! $this->is_inline) {
                Header('Content-type: image/wbmp');
            }
            if ($this->is_inline && $this->output_file != "") {
                ImageWBMP($this->img, $this->output_file);
            } else {
                ImageWBMP($this->img);
            }

            break;
        default:
            $this->PrintError('_PrintImage(): Please select an image type!');
            break;
        }
        return TRUE;
    }

    /*! 
     * Prints an error message to stdout and dies 
     */
    function PrintError($error_message) 
    {
        echo "<p><b>Fatal error</b>: $error_message<p>";
        die;
    }

    /*!
     * Prints an error message inline into the generated image and draws it centered
     * around the given coordinates (defaults to center of the image)
     *   \param error_message Message to be drawn
     *   \param where_x       X coordinate
     *   \param where_y       Y coordinate
     */
    function _DrawError($error_message, $where_x = NULL, $where_y = NULL) 
    {
        if (! $this->img)
            $this->PrintError("_DrawError(): Warning, no image resource allocated. ".
                              "The message to be written was: ".$error_message);
                              
        $ypos = (! $where_y) ? $this->image_height/2 : $where_y;
        $xpos = (! $where_x) ? $this->image_width/2 : $where_x;
        ImageRectangle($this->img, 0, 0, $this->image_width, $this->image_height,
                       ImageColorAllocate($this->img, 255, 255, 255));
       
        $this->DrawText($this->generic_font, 0, $xpos, $ypos, ImageColorAllocate($this->img, 0, 0, 0),
                        $error_message, 'center', 'center');

        $this->_PrintImage();
        return TRUE;
    }


/////////////////////////////////////////////
///////////                              MISC
/////////////////////////////////////////////

    /*!
     * ON THE WORKS:        XXX XXX XXX XXX
     *
     *****************                       __FUNCTION__ needs PHP 4.3.0
     *
     * CHECK Set*LabelParams():
     * Checks the valididy of an option.
     *  \param which_opt  String to check.
     *  \param which_acc  String of accepted choices.
     *  \param which_func Name of the calling function, for error messages.
     *  \note If checking everywhere for correctness slows things down, we could provide a
     *        child class overriding every Set...() method which uses _CheckOption(). Those new
     *        methods could proceed in the unsafe but faster way.
     */
    function _CheckOption($which_opt, $which_acc, $which_func)
    {
        $asked = trim($which_opt);
        
        // FIXME: this for backward compatibility, as eregi() fails with empty strings.
        if ($asked == '')
            return '';
            
        if (@ eregi($asked, $which_acc)) {
            return $asked;
        } else {
            $this->_DrawError("$which_func(): '$which_opt' not in available choices: '$which_acc'.");
            return NULL;
        } 
    }


    /*!
     *  Submitted by Thiemo Nagel
     */
    function SetBrowserCache($which_browser_cache) 
    {
        $this->browser_cache = $which_browser_cache;
        return TRUE;
    }

    /*!
     * Whether to show the final image or not
     * MBD: uh? what for?
     */
    function SetPrintImage($which_pi) 
    {
        $this->print_image = $which_pi;
        return TRUE;
    }
 
    function SetLineStyles($which_sls)
    {
        $this->line_style = $which_sls;
        return TRUE;
    }

    /*!
     * Expects an array with the names to be written in the graph's legend.
     */
    function SetLegend($which_leg)
    {
        if (is_array($which_leg)) { 
            $this->legend = $which_leg;
            return TRUE;
        } else { 
            $this->_DrawError('SetLegend(): Argument must be an array.');
            return FALSE;
        }
    }

    /*!
     * Specifies the absolute (relative to image's up/left corner) position
     * of the legend's upper/leftmost corner.
     *  $which_type not yet used (TODO)
     */
    function SetLegendPixels($which_x, $which_y, $which_type=NULL) 
    { 
        $this->legend_x_pos = $which_x;
        $this->legend_y_pos = $which_y;
        
        return TRUE;
    }

    /*!
     * Specifies the relative (to graph's origin) position of the legend's
     * upper/leftmost corner. MUST be called after scales are set up.
     *   $which_type not yet used (TODO)
     */
    function SetLegendWorld($which_x, $which_y, $which_type=NULL) 
    { 
        if (! $this->scale_is_set) 
            $this->_CalcTranslation();
            
        $this->legend_x_pos = $this->_xtr($which_x);
        $this->legend_y_pos = $this->_ytr($which_y);
        
        return TRUE;
    }

    function SetPlotBorderType($which_pbt) 
    {
        $this->plot_border_type = $which_pbt; //left, none, anything else = full
    }

    function SetImageBorderType($which_sibt) 
    {
        $this->image_border_type = $which_sibt; //raised, plain
    }

    
    function SetDrawPlotAreaBackground($which_dpab) 
    {
        $this->draw_plot_area_background = $which_dpab;  // TRUE or FALSE
    }

    /*!
     * Sets parameters for Data labels (labels following data points):
     *    Horizontal placement,
     *    Vertical placement,
     *    Horizontal labels type (time, data, etc...)
     *    Vertical labels type (...)
     */
    function SetDataLabelParams($which_xpos, $which_ypos, $which_xtype, $which_ytype) 
    {
        /*     
        $this->x_data_label_pos = $which_xpos;
        $this->y_data_label_pos = $which_ypos;
        $this->x_label_type = $which_xtype;
        $this->y_label_type = $which_ytype;
        */
        $this->x_data_label_pos = $this->_CheckOption($which_xpos, 'plotdown, plotup, both, plot, all, none',
                                                      __FUNCTION__);
        $this->y_data_label_pos = $this->_CheckOption($which_ypos, 'plotleft, plotright, both, plot, all, none',
                                                      __FUNCTION__);
        $this->x_label_type = $this->_CheckOption($which_xtype, 'data, title, time', __FUNCTION__);
        $this->y_label_type = $this->_CheckOption($which_ytype, 'data, time', __FUNCTION__);
        
        // Decide whether to show tick labels or not. Never if data labels are to be shown
        if ($this->x_data_label_pos !== 'none')
            $this->x_tick_label_pos == 'none';
                    
        if ($this->y_data_label_pos !== 'none')
            $this->y_tick_label_pos == 'none';
       
    }
    /*!
     * Sets parameters for Tick labels (labels next to axis ticks, following the grid subdivisions)
     *    Horizontal placement,
     *    Vertical placement,
     *    Horizontal labels type (time, data, etc...)
     *    Vertical labels type (...)
     */
     function SetTickLabelParams($which_xpos, $which_ypos, $which_xtype, $which_ytype) 
     {
        /*     
        $this->x_tick_label_pos = $which_xpos;
        $this->y_tick_label_pos = $which_ypos;
        $this->x_label_type = $which_xtype;
        $this->y_label_type = $which_ytype;
        */
        $this->x_tick_label_pos = $this->_CheckOption($which_xpos, 'plotdown, plotup, both, xaxis, all, none',
                                                      __FUNCTION__);
        $this->y_tick_label_pos = $this->_CheckOption($which_ypos, 'plotleft, plotright, both, yaxis, all, none',
                                                      __FUNCTION__);
        $this->x_label_type = $this->_CheckOption($which_xtype, 'data, title, time', __FUNCTION__);
        $this->y_label_type = $this->_CheckOption($which_ytype, 'data, time', __FUNCTION__);
 

        // Decide whether to show data labels or not. Never if tick labels are to be shown
        if ($this->x_tick_label_pos !== 'none')
            $this->x_data_label_pos == 'none';
                    
        if ($this->y_tick_label_pos !== 'none')
            $this->y_data_label_pos == 'none';

        return TRUE;
    }

    /*!
     * Sets grid parameters
     */
    function SetGridParams($which_dg, $which_dsh = TRUE)
    {
        switch ($which_dg) {
        case 'x':
            $this->draw_x_grid = TRUE;
            $this->draw_y_grid = FALSE;
            break;
        case 'y':
            $this->draw_x_grid = FALSE;
            $this->draw_y_grid = TRUE;
            break;
        case 'both':
            $this->draw_x_grid = TRUE;
            $this->draw_y_grid = TRUE;
            break;
        case 'none':
            $this->draw_x_grid = FALSE;
            $this->draw_y_grid = FALSE;
            break;
        default:
            $this->PrintError("SetGridParams(): unknown grid selection: '$which_dg'");
            return FALSE;
        }
        $this->dashed_grid = $which_dsh;
        
        return TRUE;
    }

    /*!
     * Sets the graph's title.
     */
    function SetTitle($which_title) 
    {
        $this->title_txt = $which_title;
       
        if ($which_title == '') {
            $this->title_height = 0;
            return TRUE;
        }            

        $str = split("\n", $which_title);
        $lines = count($str);
        $spacing = $this->line_spacing * ($lines - 1);
        
        if ($this->use_ttf) {
            $size = $this->_TTFBBoxSize($this->title_font['size'], 0, $this->title_font['font'], $which_title);
            $this->title_height = $size[1] * $lines;
        } else {
            $this->title_height = ($this->title_font['height'] + $spacing) * $lines;
        }   
        return TRUE;
    }
    
    /*!
     * Sets the X axis title and position.
     */
    function SetXTitle($which_xtitle, $which_xpos = 'plotdown') 
    {
        if ($which_xtitle == '')
            $which_xpos = 'none';

        $this->x_title_pos = $which_xpos;
        
        $this->x_title_txt = $which_xtitle;

        $str = split("\n", $which_xtitle);
        $lines = count($str);
        $spacing = $this->line_spacing * ($lines - 1);
        
        if ($this->use_ttf) {
            $size = $this->_TTFBBoxSize($this->x_title_font['size'], 0, $this->x_title_font['font'], $which_xtitle);
            $this->x_title_height = $size[1] * $lines;
        } else {
            $this->x_title_height = ($this->y_title_font['height'] + $spacing) * $lines;
        }
 
        return TRUE;
    }
    
   
    /*!
     * Sets the Y axis title and position.
     */
    function SetYTitle($which_ytitle, $which_ypos = 'plotleft') 
    {
        if ($which_ytitle == '')
            $which_ypos = 'none';
            
        $this->y_title_pos = $which_ypos;

        $this->y_title_txt = $which_ytitle;
        
        $str = split("\n", $which_ytitle);
        $lines = count($str);
        $spacing = $this->line_spacing * ($lines - 1);
        
        if ($this->use_ttf) {
            $size = $this->_TTFBBoxSize($this->y_title_font['size'], 90, $this->y_title_font['font'], 
                                       $which_ytitle);
            $this->y_title_width = $size[1] * $lines;
        } else {
            $this->y_title_width = ($this->y_title_font['height'] + $spacing) * $lines;
        }
        
        return TRUE;
    }

    function SetShading($which_s) 
    { 
        $this->shading = $which_s;
        return TRUE;
    }
    
    function SetPlotType($which_pt) 
    {
        $this->plot_type = $this->_CheckOption($which_pt, 
                                  'bars, lines, linepoints, area, points, pie, thinbarline, squared', 
                                  __FUNCTION__);
    }

    function SetYAxisPosition($which_pos) 
    {
        $this->y_axis_position = $which_pos;
        return TRUE;
    }
    
    function SetXAxisPosition($which_pos) 
    {
        $this->x_axis_position = $which_pos;
        return TRUE;
    }

    function SetXTimeFormat($which_xtf) 
    {
        $this->x_time_format = $which_xtf;
        return TRUE;
    }
    function SetYTimeFormat($which_ytf) 
    {
        $this->y_time_format = $which_ytf;
        return TRUE;
    }
    
    function SetXLabelAngle($which_xdla) 
    {
        $this->x_label_angle = $which_xdla;
        return TRUE;
    }

    function SetXScaleType($which_xst) 
    { 
        $this->xscale_type = $which_xst;
        return TRUE;
    }
    
    function SetYScaleType($which_yst) 
    { 
        $this->yscale_type = $which_yst;
        if ($this->x_axis_position <= 0) { 
            $this->x_axis_position = 1;
        }
        return TRUE;
    }

    function SetPrecisionX($which_prec) 
    {
        $this->x_precision = $which_prec;
        return TRUE;
    }
    function SetPrecisionY($which_prec) 
    {
        $this->y_precision = $which_prec;
        return TRUE;
    }

    function SetErrorBarLineWidth($which_seblw) 
    {
        $this->error_bar_line_width = $which_seblw;
        return TRUE;
    }

    /* FIXME: line_width should be an array, whose length and contents should be dinamically
     * modified after records_per_group 
     */
    function SetLineWidth($which_lw) 
    {
        $this->line_width = $which_lw;
        if (!$this->error_bar_line_width) { 
            $this->error_bar_line_width = $which_lw;
        }
        return TRUE;
    }

    function SetLabelScalePosition($which_blp) 
    {
        //0 to 1
        $this->label_scale_position = $which_blp;
        return TRUE;
    }

    function SetErrorBarSize($which_ebs) 
    {
        //in pixels
        $this->error_bar_size = $which_ebs;
        return TRUE;
    }

    /*!
     * Can be one of: 'tee', 'line'
     */
    function SetErrorBarShape($which_ebs) 
    {
        $this->error_bar_shape = $this->_CheckOption($which_ebs, 'tee, line', __FUNCTION__);
    }

    /*!
     * Can be one of: 'halfline', 'line', 'plus', 'cross', 'rect', 'circle', 'dot',
     * 'diamond', 'triangle', 'trianglemid'
     */
    function SetPointShape($which_pt) 
    {
        $this->point_shape = $this->_CheckOption($which_pt, 
                                'halfline, line, plus, cross, rect, circle, dot, diamond, triangle, trianglemid',
                                __FUNCTION__);
    }

    function SetPointSize($which_ps) 
    {
        //in pixels
        SetType($which_ps, 'integer');
        $this->point_size = $which_ps;

        if ($this->point_shape == "diamond" or $this->point_shape == "triangle") {
            if ($this->point_size % 2 != 0) {
                $this->point_size++;
            }
        }
        return TRUE;
    }


    /*!
     * Tells not to draw lines for missing Y data. Only works with 'lines' and 'squared' plots.
     */
    function SetDrawBrokenLines($which_bl)
    {
        $this->SetDrawBrokenLines = $which_bl;
    }

    
    /*!
     *  text-data: ('label', y1, y2, y3, ...)
     *  text-data-pie: ('label', y1), for pie charts. See DrawPieChart()
     *  data-data: ('label', x, y1, y2, y3, ...)
     *  data-data-error: ('label', x1, y1, e1+, e2-, y2, e2+, e2-, y3, e3+, e3-, ...)
     */
    function SetDataType($which_dt) 
    {
        //The next three lines are for past compatibility.
        if ($which_dt == "text-linear") { $which_dt = "text-data"; };
        if ($which_dt == "linear-linear") { $which_dt = "data-data"; };
        if ($which_dt == "linear-linear-error") { $which_dt = "data-data-error"; };

        $this->data_type = $which_dt;
        return TRUE;
    }

    /*!
     * 
     */
    function SetDataValues($which_dv) 
    {
        $this->data_values = $which_dv;
        return TRUE;
    }
    

//////////////////////////////////////////////////////////
///////////         DATA ANALYSIS, SCALING AND TRANSLATION
//////////////////////////////////////////////////////////
   
    /*!
     * Analizes data and sets up internal maxima and minima
     * Needed by: _CalcMargins()
     *   Text-Data is different than data-data graphs. For them what
     *   we have, instead of X values, is # of records equally spaced on data.
     *   text-data is passed in as $data[] = (title, y1, y2, y3, y4, ...)
     *   data-data is passed in as $data[] = (title, x, y1, y2, y3, y4, ...) 
     */
    function _FindDataLimits() 
    {
        $this->number_x_points = count($this->data_values);

        // Set some default min and max values before running through the data
        switch ($this->data_type) {
        case 'text-data':
            $minx = 0; //valid for BAR TYPE GRAPHS ONLY
            $maxx = $this->number_x_points - 1 ;  //valid for BAR TYPE GRAPHS ONLY
            $miny = (double) $this->data_values[0][1];
            $maxy = $miny;
            $this->x_label_type = 'title';
            break;
        default:  //Everything else: data-data, etc.
            $maxx = $this->data_values[0][1];
            $minx = $maxx;
            $miny = $this->data_values[0][2];
            $maxy = $miny;
            $maxy = $miny;
            break;
        }

        $this->total_records = 0;
        $max_records_per_group = 0;
        $mine = 0;  // Maximum value for the -error bar (assume error bars always > 0) 
        $maxe = 0;  // Maximum value for the +error bar (assume error bars always > 0) 
        $maxt = 0;  // Maximum number of characters in text labels
       
        // Process each row of data
        foreach($this->data_values as $dat) {

            $cnt = 0;
            
            // Extract maximum text label length
            $val = strlen(array_shift($dat));
            $maxt = ($val > $maxt) ? $val : $maxt;

            switch ($this->data_type) {
            case 'text-data':
            case 'text-data-pie':      // this one is for some pie charts, see DrawPieChart()
                //Find the relative Max and Min
                foreach ($dat as $val) {
                    settype($val, "double");
                    $maxy = ($val > $maxy) ? $val : $maxy;
                    $miny = ($val < $miny) ? $val : $miny;
                    $cnt++;
                }
                break;
            case 'data-data':
                // X-Y data is passed in as $data[] = (title, x, y, y2, y3, ...) 
                // which you can use for multi-dimentional plots.

                // X value
                $val = array_shift($dat);
                settype($val, "double");
                $maxx = ($val > $maxx) ? $val : $maxx;
                $minx = ($val < $minx) ? $val : $minx;

                foreach ($dat as $val) {
                    settype($val, "double");
                    $maxy = ($val > $maxy) ? $val : $maxy;
                    $miny = ($val < $miny) ? $val : $miny;
                    $cnt++;
                }
                break;
            case 'data-data-error':  //Assume 2-D for now, can go higher
                //Regular X-Y data is passed in as $data[] = (title, x, y, error+, error-, y2, error2+, error2-)
                
                // X value:
                $val = array_shift($dat);
                settype($val, "double");
                $maxx = ($val > $maxx) ? $val : $maxx;
                $minx = ($val < $minx) ? $val : $minx;

                while (list($key, $val) = each($dat)) {
                    settype($val, 'double');
                    if ($key%3 == 0) {
                        $maxy = ($val > $maxy) ? $val : $maxy;
                        $miny = ($val < $miny) ? $val : $miny;
                    } elseif ($key%3 == 1) {
                        $maxe = ($val > $maxe) ? $val : $maxe;
                    }   
                    elseif ($key%3 == 2) {
                        $mine = ($val > $mine) ? $val : $mine;
                    }   
                    $cnt++;
                }
                $maxy = $maxy + $maxe;
                $miny = $miny - $mine; //assume error bars are always > 0
                break;
            default:
                $this->PrintError("_FindDataLimits(): Unknown data type '$data_type'.");
            break;
            }
            
            $this->total_records += $cnt;
            
            if ($cnt > $max_records_per_group)
                $max_records_per_group = $cnt;
        }


        $this->min_x = $minx;
        $this->max_x = $maxx;
        $this->min_y = $miny;
        $this->max_y = $maxy;
        $this->max_t = $maxt;

        $this->records_per_group = ($max_records_per_group > 1) ? $max_records_per_group : 1;

        return TRUE;
    }


    /*!
     * Calculates image margins on the fly from title positions and sizes,
     * and tick labels positions and sizes.
     * \todo: add variable y_label_angle?
     *
     * FIXME: fix x_data_label_pos behaviour. Now we are leaving room for it AND x_tick_label_pos
     *        myabe it shouldn't be so...
     */
    function _CalcMargins() 
    {
        // Temporary variables for label size calculation
        $xlab = $this->_FormatLabel('x', $this->max_x);
        $ylab = $this->_FormatLabel('y', $this->max_y);

        //////// Calculate maximum X/Y axis label height and width:
        
        // TTFonts:
        if ($this->use_ttf) {
            // Maximum X axis label height
            $size = $this->_TTFBBoxSize($this->x_label_font['size'], $this->x_label_angle,
                                       $this->x_label_font['font'], $xlab);
            $this->x_tick_label_height = $size[1];

            // Maximum Y axis label width
            $size = $this->_TTFBBoxSize($this->y_label_font['size'], 0, $this->y_label_font['font'], $ylab);
            $this->y_tick_label_width = $size[0];
        }
        // Fixed fonts:
        else { 
            // Maximum X axis label height
            if ($this->x_label_angle == 90)
                $this->x_tick_label_height = strlen($xlab) * $this->x_label_font['width'];
            else 
                $this->x_tick_label_height = $this->x_label_font['height'];
                
            // Maximum Y axis label width
            $this->y_tick_label_width = strlen($ylab) * $this->y_label_font['width'];
        }

        
        ///////// Calculate margins:
        
        // Upper title, ticks and tick labels, and data labels:
        $this->y_top_margin = $this->title_height + $this->safe_margin * 2;

        if ($this->x_title_pos == 'plotup' || $this->x_title_pos == 'both')
            $this->y_top_margin += $this->x_title_height + $this->safe_margin;
            
        if ($this->x_tick_label_pos == 'plotup' || $this->x_tick_label_pos == 'both')
            $this->y_top_margin += $this->x_tick_label_height;
            
        if ($this->x_tick_pos == 'plotup' || $this->x_tick_pos == 'both')
            $this->y_top_margin += $this->x_tick_length * 2;
            
        if ($this->x_data_label_pos == 'plotup' || $this->x_data_label_pos == 'both')
            $this->y_top_margin += $this->x_tick_label_height;
            
        // Lower title, ticks and tick labels, and data labels:
        $this->y_bot_margin = $this->safe_margin * 2; 
        
        if ($this->x_title_pos == 'plotdown' || $this->x_title_pos == 'both')
            $this->y_bot_margin += $this->x_title_height;
            
        if ($this->x_tick_label_pos == 'plotdown' || $this->x_tick_label_pos == 'both')            
            $this->y_bot_margin += $this->x_tick_label_height;

        if ($this->x_tick_pos == 'plotdown' || $this->x_tick_pos == 'both')
            $this->y_bot_margin += $this->x_tick_length * 2;

        if ($this->x_data_label_pos == 'plotdown' || $this->x_data_label_pos == 'both')
            $this->y_bot_margin += $this->x_tick_label_height;
           
        // Left title, ticks and tick labels:
        $this->x_left_margin = $this->safe_margin * 2;
        
        if ($this->y_title_pos == 'plotleft' || $this->y_title_pos == 'both')
            $this->x_left_margin += $this->y_title_width + $this->safe_margin;
            
        if ($this->y_tick_label_pos == 'plotleft' || $this->y_tick_label_pos == 'both')
            $this->x_left_margin += $this->y_tick_label_width;

        if ($this->y_tick_pos == 'plotleft' || $this->y_tick_pos == 'both')
            $this->x_left_margin += $this->y_tick_length * 2 ;

        // Right title, ticks and tick labels:
        $this->x_right_margin = $this->safe_margin * 2;
        
        if ($this->y_title_pos == 'plotright' || $this->y_title_pos == 'both')
            $this->x_right_margin += $this->y_title_width + $this->safe_margin;
            
        if ($this->y_tick_label_pos == 'plotright' || $this->y_tick_label_pos == 'both')
            $this->x_right_margin += $this->y_tick_label_width;
        
        if ($this->y_tick_pos == 'plotright' || $this->y_tick_pos == 'both')
            $this->x_right_margin += $this->y_tick_length * 2;


        $this->x_tot_margin = $this->x_left_margin + $this->x_right_margin;
        $this->y_tot_margin = $this->y_top_margin + $this->y_bot_margin;
        
        return;
    }


    /*!
     * Set the margins in pixels (left, right, top, bottom)
     */
    function SetMarginsPixels($which_lm, $which_rm, $which_tm, $which_bm) 
    { 

        $this->x_left_margin = $which_lm;
        $this->x_right_margin = $which_rm;
        $this->x_tot_margin = $which_lm + $which_rm;
        
        $this->y_top_margin = $which_tm;
        $this->y_bot_margin = $which_bm;
        $this->y_tot_margin = $which_tm + $which_bm;
        
        $this->SetPlotAreaPixels();
        
        return;
    }


    /*!
     * Sets the limits for the plot area. If no arguments are supplied, uses
     * values calculated from _CalcMargins();
     * Like in GD, (0,0) is upper left
     *
     * This resets the scale if SetPlotAreaWorld() was already called
     */
    function SetPlotAreaPixels($x1=NULL, $y1=NULL, $x2=NULL, $y2=NULL) 
    {
        if ($x2 && $y2) {
            $this->plot_area = array($x1, $y1, $x2, $y2);
        } else {
            if (! isset($this->x_tot_margin))
                $this->_CalcMargins();
                
            $this->plot_area = array($this->x_left_margin, $this->y_top_margin,
                                     $this->image_width - $this->x_right_margin,
                                     $this->image_height - $this->y_bot_margin
                                    );
        }
        $this->plot_area_width = $this->plot_area[2] - $this->plot_area[0];
        $this->plot_area_height = $this->plot_area[3] - $this->plot_area[1];
        
        // Reset the scale with the new plot area.
        if (isset($this->plot_max_x))
            $this->_CalcTranslation();

        return TRUE;

    }


    /*!
     * Sets minimum and maximum x and y values in the plot using _FindDataLimits()
     * or from the supplied parameters, if any.
     *
     * This resets the scale if SetPlotAreaPixels() was already called
     */
    function SetPlotAreaWorld($xmin=NULL, $ymin=NULL, $xmax=NULL, $ymax=NULL) 
    {
        if ((! $xmin)  && (! $xmax) ) {
            // For automatic setting of data we need $this->max_x
            if (! isset($this->max_y)) {
                $this->_FindDataLimits() ;
            }
            if ($this->data_type == 'text-data') {
                $xmax = $this->max_x + 1 ;  // valid for BAR CHART TYPE GRAPHS ONLY
                $xmin = 0 ;                 // valid for BAR CHART TYPE GRAPHS ONLY
            } else {
                $xmax = $this->max_x;       // was: * 1.02
                $xmin = $this->min_x;
            }
            
            $ymax = ceil($this->max_y * 1.1);
            if ($this->min_y < 0) {
                $ymin = floor($this->min_y * 1.1);
            } else {
                $ymin = 0;
            }
        }

        $this->plot_min_x = $xmin;
        $this->plot_max_x = $xmax;

        if ($ymin == $ymax) {
            $ymax += 1;
        }
        if ($this->yscale_type == 'log') { 
            //extra error checking
            if ($ymin <= 0) { 
                $ymin = 1;
            }
            if ($ymax <= 0) {
                $this->PrintError('SetPlotAreaWorld(): Log plots need data greater than 0');
                return FALSE;
            }
        }
        
        $this->plot_min_y = $ymin;
        $this->plot_max_y = $ymax;

        if ($ymax <= $ymin) {
            $this->_DrawError('SetPlotAreaWorld(): Error in data - max not greater than min');
            return FALSE;
        }

        // Reset the scale with the new maxs and mins
        if (isset($this->plot_area_width)) {
            $this->_CalcTranslation();
        }

        return TRUE;

    } //function SetPlotAreaWorld


    /*!
     * For plots that have equally spaced x variables and multiple bars per x-point.
     */
    function _SetEqualXCoord() 
    {
        $space = ($this->plot_area[2] - $this->plot_area[0]) / ($this->number_x_points * 2) *
                 $this->group_frac_width;
        $group_width = $space * 2;
        $bar_width = $group_width / $this->records_per_group;
        //I think that eventually this space variable will be replaced by just graphing x.
        $this->data_group_space = $space;
        $this->record_bar_width = $bar_width;
        return TRUE;
    }

    /*!
     * Calculates scaling stuff...
     */
    function _CalcTranslation() 
    {
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

        // GD defines x = 0 at left and y = 0 at TOP so -/+ respectively
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

        $this->scale_is_set = TRUE;
    } // function _CalcTranslation()


    /*!
     * Translate world coordinates into pixel coordinates
     * The pixel coordinates are those of the ENTIRE image, not just the plot_area
     */
    function _xtr($x_world) 
    {
        //$x_pixels =  $this->x_left_margin + ($this->image_width - $this->x_tot_margin)*
        //      (($x_world - $this->plot_min_x) / ($this->plot_max_x - $this->plot_min_x)) ;
        //which with a little bit of math reduces to ...
        if ($this->xscale_type == "log") { 
            $x_pixels =  $this->plot_origin_x + log10($x_world) * $this->xscale ;
        } else { 
            $x_pixels =  $this->plot_origin_x + $x_world * $this->xscale ;
        }
        return round($x_pixels);
    }


    /*!
     * Translate y world coord into pixel coord
     */
    function _ytr($y_world) 
    {
        if ($this->yscale_type == "log") { 
            //minus because GD defines y = 0 at top. doh!
            $y_pixels =  $this->plot_origin_y - log10($y_world) * $this->yscale ;  
        } else { 
            $y_pixels =  $this->plot_origin_y - $y_world * $this->yscale ;  
        }
        return round($y_pixels);
    }

    /*!
     * Formats a tick or data label.
     *
     * \note Time formatting suggested by Marlin Viss
     */
    function _FormatLabel($which_pos, $which_lab) 
    { 
        switch ($which_pos) {
        case 'x':
        case 'plotx':
            switch ($this->x_label_type) {
            case 'title':
                $lab = @ $this->data_values[$which_lab][0];
                break;
            case 'data':
                $lab = number_format($which_lab, $this->x_precision, '.', ', ').$this->data_units_text;
                break;
            case 'time':
                $lab = strftime($this->x_time_format, $which_lab);
                break;
            default:
                // Unchanged from whatever format it is passed in
                $lab = $which_lab;
            break;
            }    
            break;
        case 'y':
        case 'ploty':
            switch ($this->y_label_type) {
            case 'data':
                $lab = number_format($which_lab, $this->y_precision, '.', ', ').$this->data_units_text;
                break;
            case 'time':
                $lab = strftime($this->y_time_format, $which_lab);
                break;
            default:
                // Unchanged from whatever format it is passed in
                $lab = $which_lab;
                break;
            }
            break;
        default:
            $this->PrintError("_FormatLabel(): Unknown label type $which_type");
            return NULL;
        } 
        
        return $lab;
    } //function _FormatLabel



/////////////////////////////////////////////    
///////////////                         TICKS
/////////////////////////////////////////////    

/********** 

    TODO?: Group Tick functions in 
    
        SetTickParams($which_xpos, $which_xinc, $which_xnum, $which_ypos, $which_yinc, $which_ynum)

**************/
   
    /*!
     * Use either this or SetNumXTicks() to set where to place x tick marks
     */
    function SetXTickIncrement($which_ti) 
    {
        if ($which_ti) {
            $this->x_tick_increment = $which_ti;  //world coordinates
        } else {
            if (!$this->max_x) {
                $this->_FindDataLimits();  //Get maxima and minima for scaling
            }
            //$this->x_tick_increment = ( ceil($this->max_x * 1.2) - floor($this->min_x * 1.2) )/10;
            $this->x_tick_increment =  ($this->plot_max_x  - $this->plot_min_x  )/10;
        }
        $this->num_x_ticks = ''; //either use num_y_ticks or y_tick_increment, not both
        return TRUE;
    }

    /*!
     * Use either this or SetNumYTicks() to set where to place y tick marks
     */
    function SetYTickIncrement($which_ti) 
    {
        if ($which_ti) {
            $this->y_tick_increment = $which_ti;  //world coordinates
        } else {
            if (! isset($this->max_y)) {
                $this->_FindDataLimits();  //Get maxima and minima for scaling
            }
            if (! isset($this->plot_max_y))
                $this->SetPlotAreaWorld();

            //$this->y_tick_increment = ( ceil($this->max_y * 1.2) - floor($this->min_y * 1.2) )/10;
            $this->y_tick_increment =  ($this->plot_max_y  - $this->plot_min_y  )/10;
        }
        $this->num_y_ticks = ''; //either use num_y_ticks or y_tick_increment, not both
        return TRUE;
    }


    function SetNumXTicks($which_nt) 
    {
        $this->num_x_ticks = $which_nt;
        $this->x_tick_increment = '';  //either use num_x_ticks or x_tick_increment, not both
        return TRUE;
    }
    
    function SetNumYTicks($which_nt) 
    {
        $this->num_y_ticks = $which_nt;
        $this->y_tick_increment = '';  //either use num_y_ticks or y_tick_increment, not both
        return TRUE;
    }
    
    function SetYTickPos($which_tp) 
    { 
        $this->y_tick_pos = $which_tp;  //plotleft, plotright, both, yaxis, none
        return TRUE;
    }
    function SetXTickPos($which_tp) 
    { 
        $this->x_tick_pos = $which_tp; //plotdown, plotup, both, none
        return TRUE;
    }

    function SetSkipBottomTick($which_sbt) 
    {
        $this->skip_bottom_tick = $which_sbt;
        return TRUE;
    }

    function SetXTickLength($which_ln) 
    {
        $this->x_tick_length = $which_ln;
        return TRUE;
    }
    
    function SetYTickLength($which_ln) 
    {
        $this->y_tick_length = $which_ln;
        return TRUE;
    }
    
/////////////////////////////////////////////
////////////////////          GENERIC DRAWING
/////////////////////////////////////////////
    
    /*!
     * Fills the background of the image with a solid color
     */
    function _DrawBackground() 
    {
        if (! $this->background_done) {     //Don't draw it twice if drawing two plots on one image
            ImageFilledRectangle($this->img, 0, 0, $this->image_width, $this->image_height, 
                                 $this->ndx_bg_color);
            $this->background_done = TRUE;
        }
        return TRUE;
    }

    /*!
     * Draws a border around the final image.
     */
    function _DrawImageBorder() 
    {
        switch ($this->image_border_type) {
        case 'raised':
            ImageLine($this->img, 0, 0, $this->image_width-1, 0, $this->ndx_i_light);
            ImageLine($this->img, 1, 1, $this->image_width-2, 1, $this->ndx_i_light);
            ImageLine($this->img, 0, 0, 0, $this->image_height-1, $this->ndx_i_light);
            ImageLine($this->img, 1, 1, 1, $this->image_height-2, $this->ndx_i_light);
            ImageLine($this->img, $this->image_width-1, 0, $this->image_width-1,
                      $this->image_height-1, $this->ndx_i_dark);
            ImageLine($this->img, 0, $this->image_height-1, $this->image_width-1,
                      $this->image_height-1, $this->ndx_i_dark);
            ImageLine($this->img, $this->image_width-2, 1, $this->image_width-2,
                      $this->image_height-2, $this->ndx_i_dark);
            ImageLine($this->img, 1, $this->image_height-2, $this->image_width-2,
                      $this->image_height-2, $this->ndx_i_dark);
            break;
        case 'plain':
            ImageLine($this->img, 0, 0, $this->image_width, 0, $this->ndx_i_dark);
            ImageLine($this->img, $this->image_width-1, 0, $this->image_width-1,
                      $this->image_height, $this->ndx_i_dark);
            ImageLine($this->img, $this->image_width-1, $this->image_height-1, 0, $this->image_height-1,
                      $this->ndx_i_dark);
            ImageLine($this->img, 0, 0, 0, $this->image_height, $this->ndx_i_dark);
            break;
        case 'none':
            break;
        default:
            return FALSE;
        }
        return TRUE;
    }


    /*!
     * Adds the title to the graph.
     */
    function _DrawTitle() 
    {
        // Center of the plot area
        //$xpos = ($this->plot_area[0] + $this->plot_area_width )/ 2;
        
        // Center of the image:
        $xpos = $this->image_width / 2;
        
        // Place it at almost at the top
        $ypos = $this->safe_margin;
        
        $this->DrawText($this->title_font, $this->title_angle, $xpos, $ypos,
                        $this->ndx_title_color, $this->title_txt, 'center', 'bottom'); 
         
        return TRUE; 

    }


    /*!
     * Draws the X-Axis Title
     */
    function _DrawXTitle() 
    {
        if ($this->x_title_pos == 'none')
            return;
            
        $xpos = ($this->plot_area[2] + $this->plot_area[0]) / 2;
            
        // Upper title
        if ($this->x_title_pos == 'plotup' || $this->x_title_pos == 'both') {
            $ypos = $this->safe_margin + $this->title_height + $this->safe_margin;
            $this->DrawText($this->x_title_font, $this->x_title_angle,
                            $xpos, $ypos, $this->ndx_title_color, $this->x_title_txt, 'center');
        }
        // Lower title
        if ($this->x_title_pos == 'plotdown' || $this->x_title_pos == 'both') {
            $ypos = $this->image_height - $this->x_title_height - $this->safe_margin;
            $this->DrawText($this->x_title_font, $this->x_title_angle,
                            $xpos, $ypos, $this->ndx_title_color, $this->x_title_txt, 'center');
        } 
        return TRUE;
    }

    /*!
     * Draws the Y-Axis Title
     */
    function _DrawYTitle() 
    {
        if ($this->y_title_pos == 'none')
            return;

        // Center the title vertically to the plot
        $ypos = ($this->plot_area[3] + $this->plot_area[1]) / 2;
        
        if ($this->y_title_pos == 'plotleft' || $this->y_title_pos == 'both') {
            $xpos = $this->safe_margin;
            $this->DrawText($this->y_title_font, 90, $xpos, $ypos, $this->ndx_title_color, 
                            $this->y_title_txt, 'left', 'center');
        }
        if ($this->y_title_pos == 'plotright' || $this->y_title_pos == 'both') {
            $xpos = $this->image_width - $this->safe_margin - $this->y_title_width - $this->safe_margin;
            $this->DrawText($this->y_title_font, 90, $xpos, $ypos, $this->ndx_title_color, 
                            $this->y_title_txt, 'left', 'center');
        }
        
        return TRUE;
    }


    /*!
     * Fills the plot area with a solid color
     * TODO: Images? Patterns?
     */
    function _DrawPlotAreaBackground() 
    {
        ImageFilledRectangle($this->img, $this->plot_area[0],
            $this->plot_area[1], $this->plot_area[2], $this->plot_area[3],
            $this->ndx_plot_bg_color);
    }

    /*
     * 
     */
    function DrawYAxis() 
    { 
        //Draw Line at left side or at this->y_axis_position
        if ($this->y_axis_position != "") {
            $yaxis_x = $this->_xtr($this->y_axis_position);
        } else {
            $yaxis_x = $this->plot_area[0];
        }

        ImageLine($this->img, $yaxis_x, $this->plot_area[1], 
            $yaxis_x, $this->plot_area[3], $this->ndx_grid_color);

        // Draw ticks, labels and grid, if any
        $this->_DrawYTicks();
    }
    
    /*
     *
     */
    function DrawXAxis() 
    {
        //Draw Tick and Label for Y axis
        $ylab =$this->_FormatLabel('y', $this->x_axis_position);
        if (! $this->skip_bottom_tick) { 
            $this->_DrawYTick($ylab, $this->x_axis_position);
        }

        //Draw X Axis at Y = $x_axis_postion
        ImageLine($this->img, $this->plot_area[0]+1, $this->_ytr($this->x_axis_position),
                $this->_xtr($this->plot_max_x)-1, $this->_ytr($this->x_axis_position), $this->ndx_tick_color);

        // Draw ticks, labels and grid
        // FIXME: With text-data, the vertical grid never gets drawn and sometimes
        // it is desired. 
        //if ($this->data_type != 'text-data') { //labels for text-data done at data drawing time for speed.
            $this->_DrawXTicks();
        //}
        return TRUE;
    }

    /*!
     * Draw Just one Tick, called from _DrawYTicks()
     */
    function _DrawYTick($which_ylab, $which_ypos) 
    {
        if ($this->y_axis_position != "") { 
            //Ticks and lables are drawn on the left border of yaxis
            $yaxis_x = $this->_xtr($this->y_axis_position);
        } else { 
            //Ticks and lables are drawn on the left border of PlotArea.
            $yaxis_x = $this->plot_area[0];
        }

        $y_pixels = $this->_ytr($which_ypos);

        //Lines Across the Plot Area
        if ($this->draw_y_grid) {
            if ($this->dashed_grid) {
                ImageLine($this->img, $this->plot_area[0]+1, $y_pixels, $this->plot_area[2]-1, $y_pixels, IMG_COLOR_STYLED);
            } else {
                ImageLine($this->img, $this->plot_area[0]+1, $y_pixels, $this->plot_area[2]-1,
                          $y_pixels, $this->ndx_light_grid_color);
            }
        }

        //Ticks to the Left of the Plot Area
        if (($this->y_tick_pos == "plotleft") || ($this->y_tick_pos == "both") ) { 
            ImageLine($this->img, (-$this->x_tick_length+$yaxis_x),
                      $y_pixels, $yaxis_x,
                      $y_pixels, $this->ndx_tick_color);
        }

        //Ticks to the Right of the Plot Area
        if (($this->y_tick_pos == "plotright") || ($this->y_tick_pos == "both") ) { 
            ImageLine($this->img, ($this->plot_area[2]+$this->x_tick_length),
                      $y_pixels, $this->plot_area[2],
                      $y_pixels, $this->ndx_tick_color);
        }
        
        //Ticks on the Y Axis 
        if (($this->y_tick_pos == "yaxis") ) { 
            ImageLine($this->img, $yaxis_x - $this->y_tick_length, $y_pixels, $yaxis_x, $y_pixels, $this->ndx_tick_color);
        }

        // Labels to the left of the plot area
        if ($this->y_tick_label_pos == 'plotleft' || $this->y_tick_label_pos == 'both') {
            $this->DrawText($this->y_label_font, 0, $yaxis_x - $this->y_tick_length * 1.5, 
                            $y_pixels, $this->ndx_text_color, $which_ylab, 'right', 'center');
        }
        // Labels to the right of the plot area
        if ($this->y_tick_label_pos == 'plotright' || $this->y_tick_label_pos == 'both') {
            $this->DrawText($this->y_label_font, 0, $this->plot_area[2] + $this->y_tick_length * 1.5,
                            $y_pixels, $this->ndx_text_color, $which_ylab, 'left', 'center');
        }
   }       // Function _DrawYTick()


    /*!
     * Draws Grid, Ticks and Tick Labels along Y-Axis
     * Ticks and ticklabels can be left of plot only, right of plot only, 
     * both on the left and right of plot, or crossing a user defined Y-axis
     */
    function _DrawYTicks() 
    {
        // Sets the line style for IMG_COLOR_STYLED lines (grid)
        if ($this->dashed_grid) {
            $this->_SetDashedStyle($this->ndx_light_grid_color);
            $style = IMG_COLOR_STYLED;
        } else {
            $style = $this->ndx_light_grid_color;
        }
        
        if (! $this->skip_top_tick) { //If tick increment doesn't hit the top 
            //Left Top
            //ImageLine($this->img, (-$this->tick_length+$this->_xtr($this->plot_min_x)),
            //        $this->_ytr($this->plot_max_y), $this->_xtr($this->plot_min_x), $this->_ytr($this->plot_max_y),
            //        $this->ndx_tick_color);
            //$ylab = $this->FormatYTickLabel($plot_max_y);

            //Right Top
            //ImageLine($this->img, ($this->_xtr($this->plot_max_x)+$this->tick_length),
            //        $this->_ytr($this->plot_max_y), $this->_xtr($this->plot_max_x-1), $this->_ytr($this->plot_max_y),
            //        $this->ndx_tick_color);

            //Draw Grid Line at Top
            if ($this->draw_y_grid) {
                ImageLine($this->img, $this->plot_area[0]+1, $this->_ytr($this->plot_max_y),
                              $this->plot_area[2]-1, $this->_ytr($this->plot_max_y), $style);
            }
        }

        if (! $this->skip_bottom_tick) { 
            //Right Bottom
            //ImageLine($this->img, ($this->_xtr($this->plot_max_x)+$this->tick_length),
            //        $this->_ytr($this->plot_min_y), $this->_xtr($this->plot_max_x),
            //        $this->_ytr($this->plot_min_y), $this->ndx_tick_color);

            //Draw Grid Line at Bottom of Plot
            if ($this->draw_y_grid) {
                ImageLine($this->img, $this->_xtr($this->plot_min_x)+1, $this->_ytr($this->plot_min_y),
                          $this->_xtr($this->plot_max_x), $this->_ytr($this->plot_min_y), $style);
            }
        }
        
        // maxy is always > miny so delta_y is always positive
        if ($this->y_tick_increment) {
            $delta_y = $this->y_tick_increment;
        } elseif ($this->num_y_ticks) {
            $delta_y = ($this->plot_max_y - $this->plot_min_y) / $this->num_y_ticks;
        } else {
            $delta_y =($this->plot_max_y - $this->plot_min_y) / 10 ;
        }

        $y_tmp = $this->plot_min_y;
        settype($y_tmp, 'double');
        if ($this->skip_bottom_tick) { 
            $y_tmp += $delta_y;
        }

        while ($y_tmp <= $this->plot_max_y){
            //For log plots: 
            if (($this->yscale_type == "log") && ($this->plot_min_y == 1) && 
                ($delta_y%10 == 0) && ($y_tmp == $this->plot_min_y)) { 
                $y_tmp = $y_tmp - 1; //Set first increment to 9 to get: 1, 10, 20, 30, ...
            }

            $ylab = $this->_FormatLabel('y', $y_tmp);

            $this->_DrawYTick($ylab, $y_tmp);

            $y_tmp += $delta_y;
        }

        return TRUE;

    } // function _DrawYTicks


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
    function _DrawXTicks() 
    {
        // Sets the line style for IMG_COLOR_STYLED lines (grid)
        if ($this->dashed_grid) {
            $this->_SetDashedStyle($this->ndx_light_grid_color);
            $style = IMG_COLOR_STYLED;
        } else {
            $style = $this->ndx_light_grid_color;
        }
        
/* sometimes this overlaps, and maybe it isn't that necessary...        XXX XXX XXX XXX 
        // Leftmost Tick
        ImageLine($this->img, $this->plot_area[0],
                $this->plot_area[3]+$this->x_tick_length,
                $this->plot_area[0], $this->plot_area[3], $this->ndx_tick_color);
                
        $xlab = $this->_FormatLabel('x', 0);
        
        $this->DrawText($this->x_label_font, $this->x_label_angle, $this->plot_area[0], 
                        $this->plot_area[3] + $this->x_tick_length*1.5, $this->ndx_text_color, 
                        $xlab, 'center', 'bottom');
*/
        // Calculate x increment between ticks
        if ($this->x_tick_increment) {
            $delta_x = $this->x_tick_increment;
        } elseif ($this->num_x_ticks) {
            $delta_x = ($this->plot_max_x - $this->plot_min_x) / $this->num_x_ticks;
        } else {
            $delta_x =($this->plot_max_x - $this->plot_min_x) / 10 ;
        }

        $x_tmp = $this->plot_min_x + $delta_x;  //Don't overwrite left Y axis 
        SetType($x_tmp, 'double');

        while ($x_tmp < $this->plot_max_x) {    // Was '< = ', now '<' not to overwrite rightmost y axis
        
            $xlab = $this->_FormatLabel('x', $x_tmp);
            $x_pixels = $this->_xtr($x_tmp);

            // Bottom Tick
            if ($this->x_tick_pos == 'plotdown' || $this->x_tick_pos == 'both') {
                ImageLine($this->img, $x_pixels, $this->plot_area[3] + $this->x_tick_length,
                          $x_pixels, $this->plot_area[3], $this->ndx_tick_color);
            }
            // Top Tick
            if ($this->x_tick_pos == 'plotup' || $this->x_tick_pos == 'both') {
                ImageLine($this->img, $x_pixels, $this->plot_area[1] - $this->x_tick_length,
                          $x_pixels, $this->plot_area[1], $this->ndx_tick_color);
            }
            
            // Vertical grid lines
            if ($this->draw_x_grid) {
                ImageLine($this->img, $x_pixels, $this->plot_area[1], $x_pixels, $this->plot_area[3], $style);
            }

            // Lower X axis tick labels
            if ($this->x_tick_label_pos == 'plotdown' || $this->x_tick_label_pos == 'both') {
                $this->DrawText($this->x_label_font, $this->x_label_angle, $x_pixels, 
                                $this->plot_area[3] + $this->x_tick_length*1.5, $this->ndx_text_color, 
                                $xlab, 'center', 'bottom');
            }
            // Upper X axis tick labels
            if ($this->x_tick_label_pos == 'plotup' || $this->x_tick_label_pos == 'both') {
                $this->DrawText($this->x_label_font, $this->x_label_angle, $x_pixels, 
                                $this->plot_area[1] - $this->x_tick_length*1.5, $this->ndx_text_color, 
                                $xlab, 'center', 'top');
            }
            $x_tmp += $delta_x;
        }

    } // function _DrawXTicks

    /*!
     * 
     */
    function _DrawPlotBorder() 
    {
        switch ($this->plot_border_type) {
        case "left" :
            ImageLine($this->img, $this->plot_area[0], $this->_ytr($this->plot_min_y),
                      $this->plot_area[0], $this->_ytr($this->plot_max_y), $this->ndx_grid_color);
            break;
        case "none":
            //Draw No Border
            break;
        default:
            ImageRectangle($this->img, $this->plot_area[0], $this->_ytr($this->plot_min_y),
                           $this->plot_area[2], $this->_ytr($this->plot_max_y), $this->ndx_grid_color);
            break;
        }
        $this->DrawYAxis();
        $this->DrawXAxis();
        return TRUE;
    }

   
    /*!
     * Draws the data label associated with a point in the plot.
     * This is different from x_labels drawn by _DrawXTicks() and care
     * should be taken not to draw both, as they'd probably overlap.
     * Calling of this function in DrawLines(), etc is decided after x_data_label_pos
     */
    function _DrawXDataLabel($xlab, $xpos) 
    {
        $xlab = $this->_FormatLabel('x', $xlab);
   
        // Vertical lines
        if ($this->draw_x_data_label_lines) {
            if ($this->dashed_grid) {
               $this->_SetDashedStyle($this->ndx_light_grid_color);
               imageline($this->img, $xpos, $this->plot_area[1], $xpos, $this->plot_area[3], IMG_COLOR_STYLED);
            } else {
                imageline($this->img, $xpos, $this->plot_area[1], $xpos, $this->plot_area[3], 
                          $this->ndx_light_grid_color);
            }
        }

        // Labels below the plot area
        if ($this->x_data_label_pos == 'plotdown' || $this->x_data_label_pos == 'both')
            $this->DrawText($this->x_label_font, $this->x_label_angle, $xpos, 
                            $this->plot_area[3] + $this->x_tick_length , 
                            $this->ndx_text_color, $xlab, 'center', 'bottom');
            
        // Labels above the plot area
        if ($this->x_data_label_pos == 'plotup' || $this->x_data_label_pos == 'both')
            $this->DrawText($this->x_label_font, $this->x_label_angle, $xpos, 
                            $this->plot_area[1] - $this->x_tick_length , 
                            $this->ndx_text_color, $xlab, 'center', 'top');
    }

/*    
    function DrawPlotLabel($xlab, $xpos, $ypos) 
    {
        $this->DrawText($this->x_label_font, $this->x_label_angle, $xpos, $this
*/

    /*!
     * Draws the graph legend
     *
     * \note Base code submitted by Marlin Viss
     * FIXME: maximum label length should be calculated more accurately for TT fonts
     *        Performing a BBox calculation for every legend element, for example.
     */
    function _DrawLegend($which_x1, $which_y1, $which_boxtype) 
    {
        // Find maximum legend label length
        $max_len = 0;
        foreach ($this->legend as $leg) {
            $len = strlen($leg);
            $max_len = max($max_len, $len);
        }
        $max_len += 5;          // Leave room for the boxes and margins
        
        /////// Calculate legend labels sizes:  FIXME - dirty hack - FIXME
        // TTF:
        if ($this->use_ttf) {
            $size = $this->_TTFBBoxSize($this->legend_font['size'], 0,
                                       $this->legend_font['font'], "_");
            $char_w = $size[0];

            $size = $this->_TTFBBoxSize($this->legend_font['size'], 0,
                                       $this->legend_font['font'], "|");
            $char_h = $size[1];                                       
        } 
        // Fixed fonts:
        else {
            $char_w = $this->legend_font['width'];
            $char_h = $this->legend_font['height'];
        }
        
        $v_margin = $char_h/2;                         // Between vertical borders and labels
        $dot_height = $char_h + $this->line_spacing;   // Height of the small colored boxes
        $width = $char_w * $max_len;

        //////// Calculate box size
        // upper Left
        if ( (! $which_x1) || (! $which_y1) ) {
            $box_start_x = $this->plot_area[2] - $width;
            $box_start_y = $this->plot_area[1] + 5;
        } else { 
            $box_start_x = $which_x1;
            $box_start_y = $which_y1;
        }

        // Lower right corner
        $box_end_y = $box_start_y + $dot_height*(count($this->legend)) + 2*$v_margin; 
        $box_end_x = $box_start_x + $width - 5;


        // Draw outer box
        ImageFilledRectangle($this->img, $box_start_x, $box_start_y, $box_end_x, $box_end_y, $this->ndx_bg_color);
        ImageRectangle($this->img, $box_start_x, $box_start_y, $box_end_x, $box_end_y, $this->ndx_grid_color);

        $color_index = 0;
        $max_color_index = count($this->ndx_data_color) - 1;
        
        $dot_left_x = $box_end_x - $char_w * 2;
        $dot_right_x = $box_end_x - $char_w;
        $y_pos = $box_start_y + $v_margin;
        
        foreach ($this->legend as $leg) {
            // Text right aligned to the little box
            $this->DrawText($this->legend_font, 0, $dot_left_x - $char_w, $y_pos, 
                            $this->ndx_text_color, $leg, 'right');
            // Draw a box in the data color
            ImageFilledRectangle($this->img, $dot_left_x, $y_pos + 1, $dot_right_x,
                                 $y_pos + $dot_height-1, $this->ndx_data_color[$color_index]);
            // Draw a rectangle around the box
            ImageRectangle($this->img, $dot_left_x, $y_pos + 1, $dot_right_x,
                           $y_pos + $dot_height-1, $this->ndx_text_color);
                
            $y_pos += $char_h + $this->line_spacing;
            
            $color_index++;
            if ($color_index > $max_color_index) 
                $color_index = 0;
        }
    } // Function _DrawLegend()



/////////////////////////////////////////////
////////////////////             PLOT DRAWING
/////////////////////////////////////////////

   
    /*!
     * Draws a pie chart. Data has to be 'text-data' type.
     * 
     *  This can work in two ways: the classical, with a column for each sector 
     *  (computes the column totals and draws the pie with that) 
     *  OR
     *  Takes each row as a sector and uses it's first value. This has the added
     *  advantage of using the labels provided, which is not the case with the
     *  former method. This might prove useful for pie charts from GROUP BY sql queries
     */
    function DrawPieChart() 
    {
        $xpos = $this->plot_area[0] + $this->plot_area_width/2;
        $ypos = $this->plot_area[1] + $this->plot_area_height/2;
        $diameter = min($this->plot_area_width, $this->plot_area_height);
        $radius = $diameter/2;
        $max_data_colors = count($this->ndx_data_color);
        
        // Get sum of each column? One pie slice per column
        if ($this->data_type === 'text-data') {
            foreach ($this->data_values as $row) {
                $i = 0;
                array_shift($row);                 // Label ($row[0]) unused in these pie charts
                foreach ($row as $val)
                    @ $sumarr[$i++] += abs($val);    // NOTE!  sum > 0 to make pie charts
            }
        }
        // Or only one column per row, one pie slice per row?
        else if ($this->data_type == 'text-data-pie') {
            $i = 0;
            foreach ($this->data_values as $row) {
                // Set the legend to column labels
                $legend[$i] = array_shift($row);
                $sumarr[$i] = array_shift($row);
                $i++;
            }
        }
        else {
            $this->_DrawError("DrawPieChart(): Data type '$this->data_type' not supported.");
            return FALSE;
        }
        
        $total = array_sum($sumarr);
        
        if ($total == 0) {
            $this->_DrawError('DrawPieChart(): Empty data set');
            return FALSE;
        }
        
        if ($this->shading) {
            $diam2 = $diameter / 2;
            $pie_height = $this->shading;
        } else {
            $diam2 = $diameter;
            $pie_height = 0;
        }
        
        for ($h = $pie_height; $h >= 0; $h--) {
            $color_index = 0;
            $start_angle = 0;

            $end_angle = 0;
            foreach($sumarr as $val) {
                // Reinit color index?
                $color_index = $color_index % $max_data_colors;
                
                // The last one (at the top) has a brighter color:
                if ($h == 0)
                    $slicecol = $this->ndx_data_color[$color_index];
                else                
                    $slicecol = $this->ndx_data_dark_color[$color_index];
            
                $label_txt = number_format(($val / $total * 100), $this->y_precision, ".", ", ") . "%";
                $val = 360 * ($val / $total);

                // NOTE that imagefilledarc measures angles CLOCKWISE (go figure why),
                // so the pie chart would start clockwise from 3 o'clock, would it not be
                // for the reversal of start and end angles in imagefilledarc()
                $start_angle = $end_angle;
                $end_angle += $val;
                $mid_angle = deg2rad($end_angle - ($val / 2));

                // Draw the slice       
                ImageFilledArc($this->img, $xpos, $ypos+$h, $diameter, $diam2, 
                               360-$end_angle, 360-$start_angle,
                               $slicecol, IMG_ARC_PIE);
                
                // Draw the labels only once
                if ($h == 0) {
                    // Draw the outline
                    if (! $this->shading)
                        ImageFilledArc($this->img, $xpos, $ypos+$h, $diameter, $diam2, 
                                       360-$end_angle, 360-$start_angle,
                                       $this->ndx_grid_color, IMG_ARC_PIE | IMG_ARC_EDGED |IMG_ARC_NOFILL);


                    // The '* 1.2' trick is to get labels out of the pie chart so there are more 
                    // chances they can be seen in small sectors.
                    $label_x = $xpos + ($diameter * 1.2 * cos($mid_angle)) * $this->label_scale_position;
                    $label_y = $ypos+$h - ($diam2 * 1.2 * sin($mid_angle)) * $this->label_scale_position;
                          
                    $this->DrawText($this->generic_font, 0, $label_x, $label_y, $this->ndx_grid_color,
                                    $label_txt, 'center', 'center');           
                }                                
                $color_index++;
            }   // end foreach
        }   // end for
    }


    /*!
     * Draw lines with error bars - data comes in as 
     *      array("label", x, y, error+, error-, y2, error2+, error2-, ...);
     */
    function DrawLinesError() 
    {
        $start_lines = FALSE;
        
        $max_data_colors = count($this->ndx_data_color);
        
        foreach($this->data_values as $row) {
            
            $color_index = 0;

            // First extract the text label
            $lab = array_shift($row);
            
            // Do we have a value for X?
            if ($this->data_type == 'data-data-error')
                $x_now = array_shift($row);
            else if ($this->data_type == 'text-data')
                $x_now = 0.5;     // World coordinates for first automatically placed point 
            else {
                $this->_DrawError("DrawLinesError(): Data type '$this->data_type' not supported.");
                return FALSE;
            }
                
            // Absolute coordinates:
            $x_now_pixels = $this->_xtr($x_now);
                
            // Draw X data label/line?:
            if ($this->x_data_label_pos != 'none')
               $this->_DrawXDataLabel($lab, $x_now_pixels);
               
            // Now go for Y, E+, E-
            
            // Would this be faster? Would the gain be significant?
            // foreach($row as $val) {
            // if ($i == 0)...
            // if ($i == 1)...
            // if ($i == 2)...
            // $i = $i%3;
            // }
            $i = 0;
            while (list($key, $val) = each($row)) {
                // Y
                if ($key%3 == 0) {
                    $y_now = $val;
                    $y_now_pixels = $this->_ytr($y_now);

                    $color_index = $color_index % $max_data_colors;
                        
                    $barcol = $this->ndx_data_color[$color_index];
                    $error_barcol = $this->ndx_error_bar_color[$color_index];

                    if ($start_lines) {
                        imagesetthickness($this->img, $this->line_width);
                        ImageLine($this->img, $x_now_pixels, $y_now_pixels, $lastx[$i], $lasty[$i], $barcol);
                        ImageSetThickness($this->img, 1);   // Revert to original state (for data label lines).
                    }
                    
                    $lastx[$i] = $x_now_pixels;
                    $lasty[$i] = $y_now_pixels;
                    $color_index++;
                    $i++;
                } 
                // Error+
                elseif ($key%3 == 1) {
                    $this->DrawYErrorBar($x_now, $y_now, $val, $this->error_bar_shape, $error_barcol);
                } 
                // Error-
                elseif ($key%3 == 2) {
                    $this->DrawYErrorBar($x_now, $y_now, -$val, $this->error_bar_shape, $error_barcol);
                }
            }   // end while
            $start_lines = TRUE;   // Tells us if we already drew the first column of points, 
                                   // thus having $lastx and $lasty ready for the next column.
        }   // end foreach
    }   // function _DrawErrorLines()


    /*!
     * Supported data formats: data-data-error, text-data-error (doesn't exist yet)
     * ( data comes in as array("title", x, y, error+, error-, y2, error2+, error2-, ...) )
     */
    function DrawDotsError() 
    {
        $max_data_colors = count($this->ndx_data_color);
        
        $cnt = 0;
        foreach ($this->data_values as $row) {
        
            // Reset color index 
            $color_index = 0;
            
            // First extract the label
            $lab = array_shift($row);
           
            // Do we have a value for X?
            if ($this->data_type == 'data-data-error')
                $x_now = array_shift($row);  // Read it
            else
                $x_now = 0.5 + $cnt++;       // place text-data at X = 0.5, 1.5, 2.5, etc...
                
            
            // Draw X Data labels?
            if ($this->x_data_label_pos != 'none') {
                $x_now_pixels = $this->_xtr($x_now);
                $this->_DrawXDataLabel($lab, $x_now_pixels);                   
            }     
            
            while (list($key, $val) = each($row)) {
                if ($key%3 == 0) {
                    $color_index = $color_index % $max_data_colors;
                    
                    $color = $this->ndx_data_color[$color_index];
                    $error_color = $this->ndx_error_bar_color[$color_index];

                    $y_now = $val;
                    $this->DrawDot($x_now, $y_now, $this->point_shape, $color);
                    
                    $color_index++;
                } elseif ($key%3 == 1) {
                    $this->DrawYErrorBar($x_now, $y_now, $val, $this->error_bar_shape, $error_color);
                } elseif ($key%3 == 2) {
                    $this->DrawYErrorBar($x_now, $y_now, -$val, $this->error_bar_shape, $error_color);
                }
            }
        }
    } // function DrawDotsError()


    /*
     * Supported data types: 
     *  - data-data ("title", x, y1, y2, y3, ...)
     *  - text-data ("title", y1, y2, y3, ...)
     */
    function DrawDots() 
    {
        // FIXME: What's wrong with these?
        //if ($this->data_type != 'data-data' || $this->data_type != 'text-data')
        //    $this->_DrawError("DrawDots(): Data type '$this->data_type' not supported for this plot.");
            
        $i = 0; 
        $max_data_colors = count($this->ndx_data_color);
        foreach ($this->data_values as $row) {

            // Reset color index
            $color_index = 0;
            
            // First extract the label
            $lab = array_shift($row);
            
            // Do we have a value for 'X'?
            if ($this->data_type == 'data-data') 
                $x_now = array_shift($row);
            else
                $x_now = 0.5 + $i++;    // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            $x_now_pixels = $this->_xtr($x_now);
            
            // Draw X Data labels?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now_pixels);
            
            // Proceed with Y values
            foreach ($row as $val) {
                $color_index = $color_index % $max_data_colors;
                
                if (is_numeric($val))   //Allow for missing Y data 
                    $this->DrawDot($x_now, $val, $this->point_shape, $this->ndx_data_color[$color_index]);
                    
                $color_index++;
            }
        }
    } //function DrawDots


    /*!
     * A clean, fast routine for when you just want charts like stock volume charts
     * Data must be data-data since I didn't see a graphing need for equally spaced thin lines. 
     * If you want it, then submit a feature request at 
     * http://sourceforge.net/projects/phplot/ or write to afan@jeo.net and we might add it
     */
    function DrawThinBarLines() 
    {
        //FIXME: Again, what's wrong with this check?
        //if ($this->data_type != "data-data") 
        //    $this->_DrawError("DrawThinBarLines(): Data type '$this->data_type' not supported for this plot.");
            
        $y1 = $this->_ytr($this->x_axis_position);

        $max_data_colors = count($this->ndx_data_color);
        
        $cnt = 0;
        foreach ($this->data_values as $row) {
        
            // Reset color index
            $color_index = 0;
            
            // First extract the label
            $lab = array_shift($row);
            
            // Do we have a value for 'X'?
            if ($this->data_type == 'data-data') 
                $x_now = array_shift($row);
            else
                $x_now = 0.5 + $cnt++;    // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            $x_now_pixels = $this->_xtr($x_now);
            
            // Draw X Data labels?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now_pixels);
           
            imagesetthickness($this->img, $this->line_width);
            
            foreach ($row as $val) {
                    $color_index = $color_index % $max_data_colors;
                    
                    // Draws a line from user defined x axis position up to ytr($val)
                    ImageLine($this->img, $x_now_pixels, $y1, $x_now_pixels, $this->_ytr($val),
                              $this->ndx_data_color[$color_index]);
                    
                    $color_index++;
            }
            imagesetthickness($this->img, 1);
        }
    }  //function DrawThinBarLines

    /*!
     *
     */
    // TODO TODO: add a parameter to show datalabels next to error bars?
    function DrawYErrorBar($x_world, $y_world, $error_height, $error_bar_type, $color) 
    {
        /* TODO??? sth like this.
        if ($this->x_data_label_pos == 'plot') {
            $this->DrawText($this->error_font, 90, $x1, $y2, 
                            $color, $label, 'center', 'top');
        */
        
        $x1 = $this->_xtr($x_world);
        $y1 = $this->_ytr($y_world);
        $y2 = $this->_ytr($y_world+$error_height) ;

        imagesetthickness($this->img, $this->error_bar_line_width);
        imageline($this->img, $x1, $y1 , $x1, $y2, $color);
        
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

        imagesetthickness($this->img, 1);
        return TRUE;
    }

    /*!
     * Draws a styled dot. Uses world coordinates.
     * Supported types: 'halfline', 'line', 'plus', 'cross', 'rect', 'circle', 'dot',
     * 'diamond', 'triangle', 'trianglemid'
     */
    function DrawDot($x_world, $y_world, $dot_type, $color) 
    {
        $half_point = $this->point_size / 2;
        
        $x_mid = $this->_xtr($x_world);
        $y_mid = $this->_ytr($y_world);
        
        $x1 = $x_mid - $half_point;
        $x2 = $x_mid + $half_point;
        $y1 = $y_mid - $half_point;
        $y2 = $y_mid + $half_point;

        switch ($dot_type) {
        case 'halfline':
            ImageLine($this->img, $x1, $y_mid, $x_mid, $y_mid, $color);
            break;
        case 'line':
            ImageLine($this->img, $x1, $y_mid, $x2, $y_mid, $color);
            break;
        case 'plus':
            ImageLine($this->img, $x1, $y_mid, $x2, $y_mid, $color);
            ImageLine($this->img, $x_mid, $y1, $x_mid, $y2, $color);
            break;
        case 'cross':
            ImageLine($this->img, $x1, $y1, $x2, $y2, $color);
            ImageLine($this->img, $x1, $y2, $x2, $y1, $color);
            break;
        case 'rect':
            ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $color);
            break;
        case 'circle':
            ImageArc($this->img, $x_mid, $y_mid, $this->point_size, $this->point_size, 0, 360, $color);
            break;
        case 'dot':
            ImageFilledArc($this->img, $x_mid, $y_mid, $this->point_size, $this->point_size, 0, 360, 
                           $color, IMG_ARC_PIE);
            break;
        case 'diamond':
            $arrpoints = array( $x1, $y_mid, $x_mid, $y1, $x2, $y_mid, $x_mid, $y2);
            ImageFilledPolygon($this->img, $arrpoints, 4, $color);
            break;
        case 'triangle':
            $arrpoints = array( $x1, $y_mid, $x2, $y_mid, $x_mid, $y2);
            ImageFilledPolygon($this->img, $arrpoints, 3, $color);
            break;
        case 'trianglemid':
            $arrpoints = array( $x1, $y1, $x2, $y1, $x_mid, $y_mid);
            ImageFilledPolygon($this->img, $arrpoints, 3, $color);
            break;
        default:
            ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $color);
            break;
        }
        return TRUE;
    }

    /*!
     * Draw an area plot. Supported data types:
     *      'text-data'
     *      'data-data'
     * NOTE: This function used to add first and last data values even on incomplete
     *       sets. That is not the behaviour now. As for missing data in between, 
     *       there are two posibilities: replace the point with one on the X axis (previous
     *       way), or forget about it and use the preceding and following ones to draw the polygon.
     *       There is the possibility to use both, we just need to add the method to set
     *       it. Something like SetMissingDataBehaviour(), for example.
     */
    function DrawArea() 
    {
        $max_data_colors = count($this->ndx_data_color);
        $x_axis_pos = $this->_ytr($this->x_axis_position);
        
        $cnt = 0;
        foreach ($this->data_values as $row) {
            
            // First extract the label
            $lab = array_shift($row);
                
            // Do we have a value for 'X'?
           if ($this->data_type == 'data-data') 
                $x_now = array_shift($row);
            else
                $x_now = 0.5 + $cnt++;    // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            $x_now_pixels = $this->_xtr($x_now);
            
            // Draw X Data labels?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now_pixels);
            
            // Create array of points for imagefilledpolygon()
            $i = 0;
            foreach ($row as $y_now) {
                if (is_numeric($y_now)) {     // If there's missing data do nothing.
                    $y_now_pixels = $this->_ytr($y_now);
                    $posarr[$i][] = $x_now_pixels;
                    $posarr[$i][] = $y_now_pixels;
                    $num_points[$i] = isset($num_points[$i]) ? $num_points[$i]+1 : 1;
                } else {
                    // Just to get the idea, see the note above
                    if (isset ($incomplete_data_defaults_to_x_axis)) {
                        $posarr[$i][] = $x_now_pixels;
                        $posarr[$i][] = $x_axis_pos;
                        $num_points[$i] = isset($num_points[$i]) ? $num_points[$i]+1 : 1;
                    }
                }
                $i++;
           }
        }   // end foreach

        for ($i = 0; $i < $this->records_per_group; $i++) {
            // Prepend initial points. X = first point's X, Y = x_axis_pos
            $x = $posarr[$i][0];
            array_unshift($posarr[$i], $x, $x_axis_pos);
            // Append final points. X = last point's X, Y = x_axis_pos
            $x = $posarr[$i][count($posarr[$i])-2];
            array_push($posarr[$i], $x, $x_axis_pos);
            
            $num_points[$i] += 2;
        }
        
        // Reset the color index
        $color_index = 0;
        
        // Draw the poligons
        for($cnt = 0; $cnt < $this->records_per_group ; $cnt++) {
            $color_index = $color_index % $max_data_colors;
            ImageFilledPolygon($this->img, $posarr[$cnt], $num_points[$cnt], 
                               $this->ndx_data_color[$color_index]);
            $color_index++;
        }

    } // function DrawArea()


    /*!
     * Draw Lines. Supported data-types:
     *      'data-data', 
     *      'text-data'
     * NOTE: Please see the note regarding incomplete data sets on DrawArea()
     */
    function DrawLines() 
    {
        // This will tell us if lines have already begun to be drawn.
        // It is an array to keep separate information for every line, for with a single
        // variable we could sometimes get "undefined offset" errors and no plot...
        $start_lines = array_fill(0, count($this->data_values[0]) - 1, FALSE);
        
        $max_data_colors = count($this->ndx_data_color);
        
        if ($this->data_type == "text-data") { 
            $lastx[0] = $this->_xtr(0);
            $lasty[0] = $this->_xtr(0);
        }
        $cnt = 0;
        foreach ($this->data_values as $row) {
            // Reset color index
            $color_index = 0;
            
            // First extract the label
            $lab = array_shift($row);
                
            // Do we have a value for 'X'?
           if ($this->data_type == 'data-data') 
                $x_now = array_shift($row);
            else
                $x_now = 0.5 + $cnt++;    // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            $x_now_pixels = $this->_xtr($x_now);
            
             // Draw X Data labels?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now_pixels);
 
            $i = 0; 
            foreach ($row as $y_now) {
                // Draw Lines
                if (is_numeric($y_now)) {           //Allow for missing Y data 
                    $y_now_pixels = $this->_ytr($y_now);
                    
                    $color_index = $color_index % $max_data_colors;
                    
                    $color = $this->ndx_data_color[$color_index];

                    if ($start_lines[$i] == TRUE) {
                        // Set line width, revert it to normal at the end
                        imagesetthickness($this->img, $this->line_width);
                        
                        if ($this->line_style[$i] == "dashed") {
                            $this->_SetDashedStyle($color);

                            ImageLine($this->img, $x_now_pixels, $y_now_pixels, $lastx[$i], $lasty[$i], 
                                      IMG_COLOR_STYLED);
                        } else {
                            ImageLine($this->img, $x_now_pixels, $y_now_pixels, $lastx[$i], $lasty[$i], 
                                      $color);
                        }

                        imagesetthickness($this->img, 1); 
                    }
                    $lasty[$i] = $y_now_pixels;
                    $lastx[$i] = $x_now_pixels;
                    $start_lines[$i] = TRUE;
                } 
                // Y data missing... should we leave a blank or not?
                else if ($this->draw_broken_lines) {
                    $start_lines[$i] = FALSE;
                }
                $color_index++;
                $i++;
            }
        }   // end while

    } // function DrawLines()


    /*!
     * This is a mere copy of DrawLines() with one more line drawn for each point
     * TODO: Could try to move every $start_lines check in DrawLines(),etc. out of the loops for speed.
     * TODO: Independent line width. If not, move imagesetthickness out of the loop.
     */
    function DrawSquared() 
    {
        // This will tell us if lines have already begun to be drawn.
        // It is an array to keep separate information for every line, for with a single
        // variable we could sometimes get "undefined offset" errors and no plot...
        $start_lines = array_fill(0, count($this->data_values[0]) - 1, FALSE);
        
        $max_data_colors = count($this->ndx_data_color);
        
        if ($this->data_type == "text-data") { 
            $lastx[0] = $this->_xtr(0);
            $lasty[0] = $this->_xtr(0);
        }
        $cnt = 0;
        foreach ($this->data_values as $row) {
            // Reset color index
            $color_index = 0;
            
            // First extract the label
            $lab = array_shift($row);
                
            // Do we have a value for 'X'?
           if ($this->data_type == 'data-data') 
                $x_now = array_shift($row);
            else
                $x_now = 0.5 + $cnt++;    // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            $x_now_pixels = $this->_xtr($x_now);
            
             // Draw X Data labels?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now_pixels);
 
            $i = 0; 
            foreach ($row as $y_now) {
                // Draw Lines
                if (is_numeric($y_now)) {            //Allow for missing Y data 
                    $y_now_pixels = $this->_ytr($y_now);
                    
                    $color_index = $color_index % $max_data_colors;
                    
                    $color = $this->ndx_data_color[$color_index];

                    if ($start_lines[$i] == TRUE) {
                        // Set line width, revert it to normal at the end
                        imagesetthickness($this->img, $this->line_width);
                        
                        if ($this->line_style[$i] == 'dashed') {
                            $this->_SetDashedStyle($color);
                            ImageLine($this->img, $lastx[$i], $lasty[$i], $x_now_pixels, $lasty[$i], 
                                      IMG_COLOR_STYLED);
                            ImageLine($this->img, $x_now_pixels, $lasty[$i], $x_now_pixels, $y_now_pixels, 
                                      IMG_COLOR_STYLED);
                        } else {
                            ImageLine($this->img, $lastx[$i], $lasty[$i], $x_now_pixels, $lasty[$i], $color);
                            ImageLine($this->img, $x_now_pixels, $lasty[$i], $x_now_pixels, $y_now_pixels, $color);
                        }

                        imagesetthickness($this->img, 1); 
                    }
                    $lastx[$i] = $x_now_pixels;
                    $lasty[$i] = $y_now_pixels;
                    $start_lines[$i] = TRUE;
                } 
                // Y data missing... should we leave a blank or not?
                else if ($this->draw_broken_lines) {
                    $start_lines[$i] = FALSE;
                } 
                /*
                else {
                    $lastx[$i] = $x_now_pixels;
                    $lasty[$i] = $this->_ytr(0);
                }*/
                $color_index++;
                $i++;
            }
        }   // end while
    }


        
    /*!    
     * Data comes in as $data[] = ("title", x, y, e+, e-, y2, e2+, e2-, ...);
     */
    function _DrawBars() 
    {
        if ($this->data_type != "text-data") { 
            $this->_DrawError('Bar plots must be text-data: use function SetDataType("text-data")');
            return FALSE;
        }

        $xadjust = ($this->records_per_group * $this->record_bar_width )/4;
        
        reset($this->data_values);
        while (list($j, $row) = each($this->data_values)) {

            $color_index = 0;
            $colbarcount = 0;
            $x_now = $this->_xtr($j+0.5);     // place text-data at X = 0.5, 1.5, 2.5, etc...
            
            // First extract the "title" of the row
            $lab = array_shift($row);
            
            // Draw it?
            if ($this->x_data_label_pos != 'none')
                $this->_DrawXDataLabel($lab, $x_now);
 
            while (list($k, $v) = each($row)) {
                // Draw Bars ($v)
                $x1 = $x_now - $this->data_group_space + $k*$this->record_bar_width;
                $x2 = $x1 + $this->record_bar_width*$this->bar_width_adjust; 

                if ($v < $this->x_axis_position) {
                    $y1 = $this->_ytr($this->x_axis_position);
                    $y2 = $this->_ytr($v);
                } else {
                    $y1 = $this->_ytr($v);
                    $y2 = $this->_ytr($this->x_axis_position);
                }

                if ($color_index >= count($this->ndx_data_color)) $color_index = 0;
                if ($colbarcount >= count($this->ndx_data_border_color)) $colbarcount = 0;
                $barcol = $this->ndx_data_color[$color_index];
                $bordercol = $this->ndx_data_border_color[$colbarcount];
                    
                // Allow for missing Y data 
                if (is_numeric($v)) {
                    for($i = 0;$i < $this->shading; $i++) { 
                        ImageFilledRectangle($this->img, $x1+$i, $y1-$i, $x2+$i, $y2-$i, 
                                             $this->ndx_data_dark_color[$color_index]);
                        }
                        
                    ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $barcol);
                    
                    if (!$this->shading)
                        ImageRectangle($this->img, $x1, $y1, $x2, $y2, $bordercol);
                } 
                $color_index++;
                $colbarcount++;
            }   // end while
        }   // end while
    } //function _DrawBars    


    /*!
     *
     */
    function DrawGraph() 
    {
        if ($this->img == "") {
            $this->_DrawError('DrawGraph(): No image resource allocated');
            return FALSE;
        }

        if (! is_array($this->data_values)) {
            $this->_DrawError("DrawGraph(): No array of data in \$data_values");
            return FALSE;
        }
        
        $this->_FindDataLimits();               // Get maxima and minima for scaling
        
        if ($this->total_records == 0) {           // Check for empty data sets
            $this->_DrawError('Empty data set');
            return FALSE;
        }

        $this->_CalcMargins();                   // Calculate margins

        if (! isset($this->plot_area_width))    // Set plot area pixel values (plot_area[])
            $this->SetPlotAreaPixels();

        if (! isset($this->plot_max_y))         // Set plot area world values (plot_max_x, etc.)
            $this->SetPlotAreaWorld();

        //$this->_CalcTranslation();               // Set up translation variables (Already done)
        
        if ($this->data_type == "text-data")
            $this->_SetEqualXCoord();

        $this->_DrawBackground();
        
        $this->_DrawImageBorder();

        if ($this->draw_plot_area_background)
            $this->_DrawPlotAreaBackground();
       
        $this->_DrawTitle();
        $this->_DrawXTitle();
        $this->_DrawYTitle();
        
        switch ($this->plot_type) {
        case 'bars':
            $this->_DrawPlotBorder();
            $this->_DrawBars();
            $this->DrawXAxis();
            break;
        case 'thinbarline':
            $this->_DrawPlotBorder();
            $this->DrawThinBarLines();
            break;
        case 'lines':
            $this->_DrawPlotBorder();
            if ( $this->data_type == "text-data") {
                $this->DrawLines();
            } elseif ( $this->data_type == "data-data-error") {
                $this->DrawLinesError();
            } else {
                $this->DrawLines();
            }
            break;
        case 'area':
            $this->_DrawPlotBorder();
            $this->DrawArea();
            break;
        case 'linepoints':
            $this->_DrawPlotBorder();
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
        case 'points';
            $this->_DrawPlotBorder();
            if ( $this->data_type == "text-data") {
                $this->DrawDots();
            } elseif ( $this->data_type == "data-data-error") {
                $this->DrawDotsError();
            } else {
                $this->DrawDots();
            }
            break;
        case 'pie':
            // Pie charts can maximize image space usage.
            $this->SetPlotAreaPixels($this->safe_margin, $this->title_height,
                                     $this->image_width - $this->safe_margin,
                                     $this->image_height - $this->safe_margin);
                                     
            // If we had drawn this before, it probably was too small.
            //if ($this->draw_plot_area_background)
            //    $this->_DrawPlotAreaBackground();

            $this->DrawPieChart();
            break;
        case 'squared':
            $this->_DrawPlotBorder();
            $this->DrawSquared();
            break;
        default:
            $this->_DrawPlotBorder();
            $this->_DrawBars();
            break;
        }   // end switch

        if ($this->legend)
            $this->_DrawLegend($this->legend_x_pos, $this->legend_y_pos, '');
            
        if ($this->print_image)
            $this->_PrintImage();

    } //function DrawGraph()

/////////////////////////////////////////////
//////////////////         DEPRECATED METHODS
/////////////////////////////////////////////
   
    /*!
     * Deprecated, use SetYTickPos()
     */
    function SetDrawVertTicks($which_dvt) 
    {
        if ($which_dvt != 1)
            $this->SetYTickPos('none');
        return TRUE;
    } 
        
    /*!
     * Deprecated, use SetXTickPos()
     */
    function SetDrawHorizTicks($which_dht) 
    {
        if ($which_dht != 1)
           $this->SetXTickPos('none');
        return TRUE;
    }
    
    /*!
     * \deprecated Use SetNumXTicks()
     */
    function SetNumHorizTicks($n) 
    {
        return $this->SetNumXTicks($n);
    }

    /*!
     * \deprecated Use SetNumYTicks()
     */
    function SetNumVertTicks($n) 
    {
        return $this->SetNumYTicks($n);
    }
    
    /*!
     * \deprecated Use SetXTickIncrement()
     */
    function SetHorizTickIncrement($inc) 
    {
        return $this->SetXTickIncrement($inc);
    }
    
   
    /*!
     * \deprecated Use SetYTickIncrement()
     */
    function SetVertTickIncrement($inc) 
    {
        return $this->SetYTickIncrement($inc);
    }
    
    /*!
     * \deprecated Use SetYTickPos()
     */
    function SetVertTickPosition($which_tp) 
    { 
        return $this->SetYTickPos($which_tp); 
    }
    
    /*!
     * \deprecated Use SetXTickPos()
     */
    function SetHorizTickPosition($which_tp) 
    { 
        return $this->SetXTickPos($which_tp); 
    }

    /*!
     * \deprecated Use SetFont()
     */
    function SetTitleFontSize($which_size) 
    {
        return $this->SetFont('title', $which_size);
    }
    
    /*!
     * \deprecated Use SetFont()
     */
    function SetAxisFontSize($which_size) 
    {
        $this->SetFont('x_label', $which_size);
        $this->SetFont('y_label', $whic_size);
    }
    
    /*!
     * \deprecated Use SetFont()
     */
    function SetSmallFontSize($which_size) 
    {
        return $this->SetFont('generic', $which_size);
    }

    /*!
     * \deprecated Use SetFont()
     */
    function SetXLabelFontSize($which_size) 
    {
        return $this->SetFont('x_title', $which_size);
    }
    
    /*!
     * \deprecated Use SetFont()
     */
    function SetYLabelFontSize($which_size) 
    {
        return $this->SetFont('y_title', $which_size);
    }

    /*!
     * \deprecated Use SetXTitle()
     */ 
    function SetXLabel($which_xlab) 
    {
        return $this->SetXTitle($which_xlab);
    }

    /*!
     * \deprecated Use SetYTitle()
     */ 
    function SetYLabel($which_ylab) 
    {
        return $this->SetYTitle($which_ylab);
    }   
    
    /*!
     * \deprecated This is now an Internal function - please set width and 
     *             height via PHPlot() upon object construction
     */
    function SetImageArea($which_iw, $which_ih) 
    {
        $this->image_width = $which_iw;
        $this->image_height = $which_ih;

        return TRUE;
    }

    /*!
     * \deprecated Use SetXTickLength() and SetYTickLength() instead.
     */
    function SetTickLength($which_tl) 
    {
        $this->SetXTickLength($which_tl);
        $this->SetYTickLength($which_tl);
        return TRUE;
    }

    /*!
     * \deprecated  Use SetTickLabelParams()
     */
    function SetYGridLabelType($which_yglt) 
    {
        $this->SetTickLabelParams($this->x_tick_label_pos, $this->y_tick_label_pos, 
                                  $this->x_label_type, $which_yglt);
        return TRUE;
    }

    /*!
     * \deprecated  Use SetTickLabelParams()
     */
    function SetXGridLabelType($which_xglt) 
    {
        $this->SetTickLabelParams($this->x_tick_label_pos, $this->y_tick_label_pos, $which_xglt, 
                                  $this->y_label_type);
        return TRUE;
    }
    /*!
     * \deprecated  Use SetTickLabelParams()
     */
    function SetYGridLabelPos($which_yglp) 
    {
        $this->SetTickLabelParams($this->x_tick_label_pos, $which_yglp, $this->x_label_type, 
                                  $this->y_label_type);
        return TRUE;
    }
    /*!
     * \deprecated Use SetTickLabelParams()
     */
    function SetXGridLabelPos($which_xglp) 
    {
        $this->SetTickLabelParams($which_xglp, $this->y_tick_label_pos, $this->x_label_type, 
                                  $this->y_label_type);
        return TRUE;
    }

    /*!
     * \deprecated Use SetGridParams()
     */
    function SetDrawYGrid($which_dyg) 
    {
        $this->draw_y_grid = $which_dyg;  // 1 = TRUE or anything else = FALSE
    }
    
    /*!
     * \deprecated use SetGridParams()
     */
    function SetDrawXGrid($which_dxg) 
    {
        $this->draw_x_grid = $which_dxg;  // 1 = TRUE or anything else = FALSE
    }
    
    /*!
     * \deprecated Use SetGridParams()
     */
    function SetDashedGrid($which_dsh) 
    {
        $this->dashed_grid = $which_dsh;    // 1 = TRUE, 0 = FALSE
    }

    /*!
     * \deprecated Use SetXtitle()
     */
    function SetXTitlePos($xpos) 
    {
        $this->x_title_pos = $xpos;
        return TRUE;
    }
    
    /*!
     * \deprecated Use SetYTitle()
     */
    function SetYTitlePos($xpos) 
    {
        $this->y_title_pos = $xpos;
        return TRUE;
    }
    
    /*!
     * \deprecated  Use DrawDots()
     */
    function DrawDotSeries() 
    {
        $this->DrawDots();
    }
    
    /*!
     * \deprecated Use SetXLabelAngle()
     */
    function SetXDataLabelAngle($which_xdla) 
    {
        return $this->SetXLabelAngle($which_xdla);
    }
    
    /*!
     * Draw Labels (not grid labels) on X Axis, following data points. Default position is 
     * down of plot. Care must be taken not to draw these and x_tick_labels as they'd probably overlap.
     *
     * \deprecated Use SetDataLabelParams() instead
     */
    function SetDrawXDataLabels($which_dxdl) 
    {
        if ($which_dxdl == '1' )
            $this->SetDataLabelParams('plotdown', $this->y_data_label_pos, 
                                      $this->x_label_type, $this->y_label_type);
        else
            $this->SetDataLabelParams('none', $this->y_data_label_pos, 
                                      $this->x_label_type, $this->y_label_type);
    }

    /*!
     * \deprecated This method was intended to improve performance by being specially 
     * written for 'data-data'. However, the improvement didn't pay. Use DrawLines() instead
     */
    function DrawLineSeries() 
    {
        return $this->DrawLines();
    }

    /*!
     * \deprecated Calculates maximum X-Axis label height. Now inside _CalcMargins()
     */
    function CalcXHeights() 
    {
        // TTF
        if ($this->use_ttf) {
            $xstr = str_repeat('.', $this->max_t);
            $size = $this->_TTFBBoxSize($this->x_label_font['size'], $this->x_label_angle,
                                       $this->x_label_font['font'], $xstr);
            $this->x_tick_label_height = $size[1];
        } 
        // Fixed font
        else { // For Non-TTF fonts we can have only angles 0 or 90
            if ($this->x_label_angle == 90)
                $this->x_tick_label_height = $this->max_t * $this->x_label_font['width'];
            else 
                $this->x_tick_label_height = $this->x_label_font['height'];
        }

        return TRUE;
    }


    /*!
     * \deprecated Calculates Maximum Y-Axis tick label width. Now inside _CalcMargins()
     */
    function CalcYWidths() 
    {
        //the "." is for space. It isn't actually printed
        $ylab = number_format($this->max_y, $this->y_precision, ".", ", ") . $this->data_units_text . ".";
        
        // TTF
        if ($this->use_ttf) {
            // Maximum Y tick label width
            $size = $this->_TTFBBoxSize($this->y_label_font['size'], 0, $this->y_label_font['font'], $ylab);
            $this->y_tick_label_width = $size[0];
            
        } 
        // Fixed font
        else {
            // Y axis title width
            $this->y_tick_label_width = strlen($ylab) * $this->y_label_font['width'];
        }

        return TRUE;
    }

    /*!
     * \deprecated Superfluous.
     */
    function DrawLabels() 
    {
        $this->_DrawTitle();
        $this->_DrawXTitle();
        $this->_DrawYTitle();
    }
    
    /*! 
     * Set up the image resource 'img'
     * \deprecated The constructor should init 'img'
     */
    function InitImage() 
    {
        $this->img = ImageCreate($this->image_width, $this->image_height);

        if (! $this->img)
            $this->PrintError("InitImage(): Could not create image resource");
        return TRUE;
    }

    /*!
     * \deprecated
     */
    function SetNewPlotAreaPixels($x1, $y1, $x2, $y2) 
    {
        //Like in GD 0, 0 is upper left set via pixel Coordinates
        $this->plot_area = array($x1, $y1, $x2, $y2);
        $this->plot_area_width = $this->plot_area[2] - $this->plot_area[0];
        $this->plot_area_height = $this->plot_area[3] - $this->plot_area[1];
        $this->y_top_margin = $this->plot_area[1];
        
        if (isset($this->plot_max_x))
            $this->_CalcTranslation();

        return TRUE;
    }

    /*!
     * \deprecated Use _SetRGBColor()
     */
    function SetColor($which_color) 
    { 
        $this->_SetRGBColor($which_color);
        return TRUE;
    }
 
    
}  // class PHPlot
?>
