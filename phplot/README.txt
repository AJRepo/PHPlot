This routine is a class for creating scientific and business
charts. To run the test data extract the files with
	tar -zxvf phplot-3.01.tar.gz 
and then point your browser to 
	format-chart.php.

There are some configuration settings that you will need
to make based on your setup. 

1. File Type: Depending on the version of GD you are using, 
you may or may not have GIF or PNG file ability. That is 
set with the function. 
	SetFileFormat("<filetype>") where filetype is png, gif, jpeg, ...

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

Version History
Oct 25, 2000: Version 3.01: errorbars and different types of data
Aug 15, 2000: Version 3: added dots, lines, area, and pie charts
Aug 12, 1999: version 2: added bars that can be < 0

