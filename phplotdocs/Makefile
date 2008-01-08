# $Id$
# Makefile for PHPlot Programmer's Reference Manual (Docbook-xml version)
# lbayuk@pobox.com (based on other projects)
# This uses xsltproc for HTML.
# PDF does not work.

# === Makefile Configuration ===

# Name of this package, for the release target:
PACKAGE=phplotdocs

# PHP CLI to run examples:
PHP=/opt/php/bin/php

# XSL Style Sheet for HTML:
XSL_HTML=style.xsl
# Style Sheet for Print/PDF: (TBSL)
XSL_PRINT=

# XSL Transformation Processor program:
XSLTPROC=xsltproc

# Command to build a PDF. Append output name and input name. Does not work.
# simple, native, or db2latex
DBLATEX_STYLE=simple
# Add -d for debug: leave temp files
MAKEPDF=dblatex -T $(DBLATEX_STYLE) -d -b pdftex -o

# === End of Makefile Configuration ===

# Primary source file, which includes all the others:
MAIN=main.xml

# List of all source files:
SRC=$(MAIN) $(XSL_HTML) \
  reference.xml install.xml quickstart.xml \
  concepts.xml examples.xml functions.xml changelog.xml developer.xml

# Files to add to release, in addition to *.html files:
ALSO=phplotdoc.css

# Files to add to the source release:
SRCALSO=Makefile README ChangeLog

# All files to (1) include in source release, and (2) tag in RCS:
RELFILES=$(SRC) $(ALSO) $(SRCALSO)

# Release base name:
RELEASE=$(PACKAGE)
# Source release base name:
SRCRELEASE=$(PACKAGE)-src

# All PHP example source files:
EXAMPLES_PHP=$(wildcard examples/*.php)

# Example XML files, generated from PHP scripts:
EXAMPLES_XML=$(EXAMPLES_PHP:.php=.xml)

# Example image files, also generated from PHP scripts:
EXAMPLES_IMAGES=$(EXAMPLES_PHP:.php=.png)

# Entity list file for examples:
# This is referenced by the manual source files, and is the file used
# for testing if the examples are up-to-date:
EXAMPLE_LIST=examples/list.xml

# =====

# Targets:

all: html
html: html.mark
pdf: phplot.pdf
# Special version of the manual with required logo for
# Sourceforge project web pages. See the XSLT style sheet for details.
sfhtml:
	$(MAKE) -$(MAKEFLAGS) XSLTPROCFLAGS='--param footerlogo 1' html

# Version date stamp file:
version.xml:
	echo "<!ENTITY version \"`date +%Y-%m-%d`\">" > $@

# Build (X)HTML Manual. The html.mark file is just used for dependency.
html.mark: $(SRC) $(ALSO) version.xml $(EXAMPLE_LIST)
	$(XSLTPROC) $(XSLTPROCFLAGS) $(XSL_HTML) $(MAIN)
	touch html.mark


# Build PDF manual with dblatex.
# This doesn't work yet. Testing: PDF with dblatex
phplot.pdf: $(SRC) $(ALSO) $(EXAMPLE_LIST)
	$(MAKEPDF) $@ $(MAIN)

# Just build the example image files. (Can't use 'examples' as a target,
# because there is a directory by that name which interferes.)
exampleimages: $(EXAMPLES_IMAGES)

# Cleanup: deletes intermediate files, leaves manual.
clean:
	-rm -f html.mark version.xml
	-rm -f $(EXAMPLES_XML) $(EXAMPLE_LIST)

# All cleanup: Remove all generated files. Caution: Removes all HTML files!
allclean: clean
	-rm -f *.html main.pdf
	-rm -f $(EXAMPLES_IMAGES)

# Make a release of the HTML documentation only:
# This assumes certain extensions in images/ (only *.png) and examples/
release: html
	mkdir $(RELEASE) $(RELEASE)/images $(RELEASE)/examples
	cp *.html $(ALSO) $(RELEASE)
	cp images/*.png $(RELEASE)/images
	cp examples/*.png $(RELEASE)/examples
	zip -r $(RELEASE).zip $(RELEASE)
	rm -rf $(RELEASE)
	@echo Created release $(RELEASE) as $(RELEASE).zip

# Make a source release:
# This assumes certain extensions in images/ (only *.png) and examples/
dist: $(RELFILES)
	mkdir $(RELEASE) $(RELEASE)/images $(RELEASE)/examples
	cp $(RELFILES) $(RELEASE)
	cp images/*.png $(RELEASE)/images
	cp examples/*.php $(RELEASE)/examples
	zip -r $(SRCRELEASE).zip $(RELEASE)
	rm -rf $(RELEASE)
	@echo Created source distribution $(SRCRELEASE) as $(SRCRELEASE).zip

# === For building the examples ===

# Create the example entity list file.
# The XML and IMAGES files are listed as a dependency so the whole thing
# will be rebuilt if an example source changes.
$(EXAMPLE_LIST): $(EXAMPLES_PHP) $(EXAMPLES_XML) $(EXAMPLES_IMAGES)
	{ echo "<!-- Automatically generated: entity list file -->"; \
      for f in $(notdir $(EXAMPLES_PHP:.php=)); do \
        echo "<!ENTITY $$f SYSTEM \"$$f.xml\">"; done; } > $@

# Rules for examples:

# This rule turns a PHP file into an XML file for inclusion as an example.
# Based on one in the Linux Kernel Documentation DocBook Makefile.
%.xml: %.php
	{ echo "<programlisting>"; \
      expand --tabs=8 $< \
      | sed -e "s/&/\\&amp;/g" -e "s/</\\&lt;/g" -e "s/>/\\&gt;/g"; \
      echo "</programlisting>"; } > $@

# This rule runs an example through PHP to get an image file:
%.png: %.php
	$(PHP) $< > $@

# ===
