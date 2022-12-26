# Makefile for PHPlot Test Suite
# $Id$
# This Makefile is used to make a zip file release of the test suite.

# Staging area, zip file base name, and directory when unpacked:
PACKAGE=phplottest

# Generate a list of all test scripts from the tests.ini configuration file:
# This command just changes [x] into x.php and ignores other lines.
TESTS := $(shell sed -n 's/^\[\([^]]*\).*/\1.php/p' tests.ini)

# Extra files needed for testing:
EXTRA=run_test.php compare_tests.php \
  config.php usupport.php esupport.php testclass.php makedata.php \
  tests.ini transparent.html drawtext.php ohlcdata.csv ex-pielabeltypedata.php

# Additional files to release:
ETCFILES=README LICENSES Makefile ChangeLog

# Static images and other non-text used by tests, in IMAGES/*
IMAGES=\
  images/FreeUniversal-Regular.ttf \
  images/bubbles.png \
  images/clouds.jpg \
  images/drop.png \
  images/graygradient.png \
  images/tile1.png \
  images/tile2.png \
  images/watermark.png \


# All files which need to be released:
ALLFILES=$(TESTS) $(EXTRA) $(ETCFILES)

# No default action - give help instead.
default:
	@echo "Usage: make release"
	@echo "  release: Package the tests into a ZIP file."

# Make a release snapshot:
release: $(ALLFILES)
	mkdir $(PACKAGE) $(PACKAGE)/images || exit 1
	cp -v -p $(ALLFILES) $(PACKAGE)/
	cp -v -p $(IMAGES) $(PACKAGE)/images/
	zip -r $(PACKAGE).zip $(PACKAGE)/
	rm -rf $(PACKAGE)
	@echo "Release package is: $(PACKAGE).zip"
