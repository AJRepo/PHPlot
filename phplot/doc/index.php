<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- $Id$ -->
<html>
<head>
    <title>Welcome to PHPlot</title>
    <link type="text/css" rel="stylesheet" href="style.css" />
</head>
<body>

<h2>Welcome to PHPlot</h2>
<table border="0">
 <tr>
  <td valign="top">
   <p>
   PHPlot is a <a href="http://www.php.net">PHP4</a> class for on the fly graphs
   generation. It was started by
   <a href="mailto:afan AT jeo DOT net">Afan Ottenheimer</a> in 2000 as an
   opensource project, and is now co-developed thanks to 
   <a href="http://sourceforge.net">sourceforge</a>. It is distributed under
   the terms of the 
   <a href="http://www.gnu.org/copyleft/gpl.html"> GNU General Public License</a>,
   and the <a href="http://www.php.net/license.html">PHP license</a>. You can always
   obtain the latest source from the <a href="http://sourceforge.net/projects/phplot/">
  sourceforge project page</a>, please check CVS, we try to have it always working
  there.
  </p>
  <p>For further information, please check <a href="http://www.jeo.net/php/phplot/">
  Afan's site</a>
  </p>
  </td>
  <td>
   <table border="0">
   <tr><td><img src="imgs/graph1.png" /></td></tr>
   <tr><td class="imgfoot">Example line graph with labels, legend
       and left and lower axis titles.</td></tr>
   </table>
  </td>
 </tr>
</table>
<h3>Features</h3>
<table border="0">
 <tr>
  <td>
   <table border="0">
   <tr><td><img src="imgs/graph3.png" /></td></tr>
   <tr><td class="imgfoot">Example 3d pie chart.</td></tr>
  </table>
  </td>
  <td>
   <p>
   Here goes a (incomplete) list, in no particular order.:
   <ul>
    <li>Several different graph types: lines, bars, points, areas, pie. </li>
    <li>3D shading for pie and bar graphs.</li>
    <li>Different line types, customizable. </li>
    <li>Can draw error margins along y-axis when supplied in data. </li>
    <li>Highly customizable canvas: titles, labels and ticks can be 
        placed anywhere, with any color.</li>
    <li>Grids.</li>
    <li>Legend. Different types on the works. </li>
    <li>TrueType font support. </li>
    <li>Linear and logaritmic scales.</li>
    <li>Several output formats: jpeg, png, gif, wbmp (those supported by your GD)</li>
   </ul>
   And here a short whishlist:
   <ul>
    <li>(fillme)</li>
    <li>(fillme)</li>
   </ul>
   </p>
  </td>
 </tr>
</table> 


<h3>Requirements</h3>
<p>
We are not sure about exact requirements, but at least PHP 4.0.6 and 
GD Lib 2 are necessary.
</p>


<h3>Test and examples</h3>
<p>
These examples make use of many, but not all, of the features present in PHPlot.
The best one is the example-o-matic, where you can alter many parameters. Please proceed
to any of them:
<ul>
    <li><a href="../examples/test_setup.php">Setup test</a></li>
    <li>Examples:</li>
        <ul>
            <li><a href="../examples/format_chart.php">Example-o-matic</a>, 
            needs some user interaction, though</li>
        <li><a href="../examples/example1.php">Chart</a></li>
        <li><a href="../examples/example4.php">Stock Chart</a></li>
        </ul>
    <li><b>NOTE:</b>If the examples don't seem to work for you, it may be that you
        don't have PHP set up correctly.If you do you should see some bold text here:
        <?php echo "<b>OK!</b>\n"; ?> <br />
        If you see no link then you should contact your system
        administrator or your webserver documentation as to how to configure this.
    </li>
</ul>
</p>

<h3>Internals</h3>
<p>
Description of the use and inner workings of PHPlot:
<ul>
    <li><a href="schema.html">PHPlot canvas' elements drawing</a></li>
    <li>Function reference: (<b>Very</b> outdated)
        <ul>
            <li><a href="user_functions.html">User functions</a></li>
            <li><a href="user_internal_functions.html">User/Internal functions</a></li>
            <li><a href="internal_functions.html">Internal functions (only of interest 
                for developers)</a></li>
        </ul>
    </li>    
</ul>
</p>

<h3>The Authors</h3>
<p>
<ul>
    <li>Please fill me with your info...</li>
    <li>Please fill me with your info...</li>
    <li>Please fill me with your info...</li>
    <li>Please fill me with your info...</li>
</p>

<p class="foot">$Id$</p>
</body>
</html>
