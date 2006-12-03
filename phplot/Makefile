# Makefile for tag and release of PHPLot
# $Id$

# Project name:
PROJ=phplot

# List of files to tag and release:
REL=ChangeLog LICENSE.GPL LICENSE.PHP_3_0 NEWS.txt README.txt \
   phplot.php phplot_data.php rgb.inc.php

# Temporary directory for building releases. Can be relative.
# Release packages will be left here too.
TMP=../tmp

# Release directory name. VER comes from command line on "make VER=xxx release"
RDIR=$(PROJ)-$(VER)
# Release directory path:
RDIRPATH=$(TMP)/$(RDIR)

default:
	@echo "Usage:  make TAG=v tag : Tag release files in CVS with 'v'"
	@echo "    Example:  make TAG=rel5_0rc3 tag"
	@echo "Usage:  make VER=v release : Make release packages"
	@echo "    Example:  make VER=5.0rc3 release"

tag:
	@if [[ x$(TAG) = x ]]; then echo "Error: must set TAG variable"; exit 1; fi
	cvs tag $(TAG) $(REL)

release:
	@if [[ x$(VER) = x ]]; then echo "Error: must set VER variable"; exit 1; fi
	@if [[ $$(id -u) -ne 0 ]]; then \
          echo "Warning: Not root, files in .tar will have wrong ownership"; fi
	@echo "Note: The main script has the following version info:"
	@grep -i 'phplot version' phplot.php
	mkdir -p $(RDIRPATH)
	cp -v -p $(REL) $(RDIRPATH)
	if [[ $$(id -u) -eq 0 ]]; then chown -R 0:0 $(RDIRPATH); fi
	(cd $(TMP); zip -r $(RDIR).zip $(RDIR); )
	(cd $(TMP); tar -cvzf $(RDIR).tar.gz $(RDIR); )
	rm -rf $(RDIRPATH)
	@echo "Release packages are: $(TMP)/$(RDIR).zip"
	@echo "                 and: $(TMP)/$(RDIR).tar.gz"
