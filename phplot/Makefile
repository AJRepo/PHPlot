# Makefile for release of PHPLot
# $Id$

# Project name:
PROJ=phplot

# List of text files to release, and do CRLF line ending
# conversions in the ZIP release only:
REL_TXTFILES=COPYING ChangeLog NEWS.txt README.txt NEWS_part1.txt
# List of all top-level files to release:
REL=$(REL_TXTFILES) phplot.php rgb.inc.php

# Contrib files to get CRLF line ending conversion in the Zip release:
REL_CONTRIB_TXTFILES=contrib/README.txt
# Contrib files to release:
REL_CONTRIB=$(REL_CONTRIB_TXTFILES) \
  contrib/color_range.example.php \
  contrib/color_range.php \
  contrib/color_range.test1.php \
  contrib/color_range.test2.php \
  contrib/data_table.example1.php \
  contrib/data_table.example2.php \
  contrib/data_table.example3.php \
  contrib/data_table.php \
  contrib/prune_labels.example.php \
  contrib/prune_labels.php \
  contrib/prune_labels.test.php \


# Temporary directory for building releases. Can be relative.
# Release packages will be left here too.
TMP=../tmp

# Release directory name. VER comes from command line on "make VER=xxx release"
RDIR=$(PROJ)-$(VER)
# Release directory path:
RDIRPATH=$(TMP)/$(RDIR)

default:
	@echo "Usage:  make VER=v release : Make release packages"
	@echo "    Example:  make VER=5.0.1 release"

release:
	@if [[ x$(VER) = x ]]; then echo "Error: must set VER variable"; exit 1; fi
	@echo "Note: The main script has the following version info:"
	@grep -i 'phplot version' phplot.php
	mkdir -p $(RDIRPATH) $(RDIRPATH)/contrib
	cp -v -p $(REL) $(RDIRPATH)
	cp -v -p $(REL_CONTRIB) $(RDIRPATH)/contrib
	(cd $(TMP); tar -cvzf $(RDIR).tar.gz --owner=0 --group=0 $(RDIR); )
	(cd $(RDIRPATH); for f in $(REL_TXTFILES) $(REL_CONTRIB_TXTFILES);do todos < $$f > convert.tmp && mv -f convert.tmp $$f; done; )
	(cd $(TMP); zip -r $(RDIR).zip $(RDIR); )
	rm -rf $(RDIRPATH)
	@echo "Release packages are: $(TMP)/$(RDIR).zip"
	@echo "                 and: $(TMP)/$(RDIR).tar.gz"
