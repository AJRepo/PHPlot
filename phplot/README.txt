This routine is a class for creating scientific and business
charts. To run the test data extract the files with
	tar -zxvf phplot-4.0.1.tar.gz 
and then point your browser to 
	examples/format-chart.php.

There are some configuration settings that you will need
to make based on your setup. 

1. File Type: Depending on the version of GD you are using, 
you may or may not have GIF or PNG file ability. That is 
set with the function. 
	SetFileFormat("<filetype>") where <filetype> is png, gif, jpeg, ...
or edit the file phplot.php and make the line
	var $file_format = "<filetype>";

2. TTF: If you have TTF installed then use 
	SetUseTTF("1");
otherwise use
	SetUseTTF("0");

Everything else should be independent of what version you are using.
This has been tested with PHP3, PHP4, GD1.2 and GD 3.8.

To start with a test see format_chart.php


/* Copyright (C) 1998,1999,2000 Afan Ottenheimer, afan@jeo.net
This is distributed with NO WARRANTY and under the terms of the GNU GPL
and PHP.
If you use it - a cookie or some credit would be nice.
There is one exception - a license is required if this is used
or distributed by Microsoft.
You can get a copy of the GNU GPL at http://www.gnu.org/copyleft/gpl.html
You can get a copy of the PHP License at http://www.php.net/license.html

 3.6.3 (fix in DrawBars)
 3.6.2 (adds DashedLine style for graphing data)
 3.6.0 (adds DrawLegend, and changes position of y labels)
 3.4.1 (same as 3.4.0 but for y axis also, adds 'none' as label type)
 3.4.0 (adds X data in unixtime printed with strftime and minor fixes) 
Oct 25, 2000: Version 3.01: errorbars and different types of data
Aug 15, 2000: Version 3: added dots, lines, area, and pie charts
Aug 12, 1999: version 2: added bars that can be < 0
