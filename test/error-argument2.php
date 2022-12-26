<?php
# $Id$
# PHPlot error test - argument error with handler
require 'esupport.php';
set_error_handler('test_catch_exit');
require 'error-argument.php';
